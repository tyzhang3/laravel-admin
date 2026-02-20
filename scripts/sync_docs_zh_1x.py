#!/usr/bin/env python3
"""
Sync laravel-admin Chinese 1.x docs from https://laravel-admin.org/docs/zh/1.x
into local docs/zh as Markdown files.
"""

from __future__ import annotations

import argparse
import json
import re
from collections import OrderedDict
from datetime import datetime, timezone
from pathlib import Path
from typing import Iterable

import requests
from bs4 import BeautifulSoup, Tag
from markdownify import markdownify as to_markdown


DEFAULT_BASE_URL = "https://laravel-admin.org/docs/zh/1.x"
DEFAULT_TARGET_DIR = "docs/zh"


def normalize_slug(href: str) -> str | None:
    m = re.search(r"/docs/zh/1\.x/?([^#?]*)", href)
    if not m:
        return None
    slug = m.group(1).strip("/")
    return slug or "README"


def file_name_for_slug(slug: str) -> str:
    return "README.md" if slug == "README" else f"{slug}.md"


def abs_doc_url(base_url: str, slug: str) -> str:
    if slug == "README":
        return f"{base_url}/README"
    return f"{base_url}/{slug}"


def fetch_soup(session: requests.Session, url: str, timeout: int) -> BeautifulSoup:
    res = session.get(url, timeout=timeout)
    res.raise_for_status()
    return BeautifulSoup(res.text, "lxml")


def extract_slugs(index_soup: BeautifulSoup) -> list[str]:
    links = index_soup.select("aside a[href^='/docs/zh/1.x']")
    ordered: "OrderedDict[str, None]" = OrderedDict()
    for a in links:
        href = a.get("href", "")
        slug = normalize_slug(href)
        if slug:
            ordered[slug] = None
    return list(ordered.keys())


def extract_body_html(page_soup: BeautifulSoup) -> tuple[str, str]:
    pjax = page_soup.select_one("#pjax-container")
    if not isinstance(pjax, Tag):
        raise RuntimeError("Missing #pjax-container")

    h1 = pjax.select_one(".content-header h1")
    title = h1.get_text(strip=True) if isinstance(h1, Tag) else ""

    content = pjax.select_one("div.content.px-2")
    if not isinstance(content, Tag):
        raise RuntimeError("Missing content body")

    for script in content.select("script"):
        script.decompose()
    for style in content.select("style"):
        style.decompose()

    # Remove table-of-contents + ad row that is injected before article content.
    for child in list(content.children):
        if isinstance(child, Tag) and "row" in child.get("class", []) and child.select_one(".toc"):
            child.decompose()
            break

    body_html = "".join(str(node) for node in content.contents).strip()
    if not body_html:
        raise RuntimeError("Empty article body")

    return title, body_html


def html_to_markdown(title: str, body_html: str) -> str:
    md = to_markdown(
        body_html,
        heading_style="ATX",
        bullets="-",
        autolinks=True,
    ).strip()
    if title and not md.startswith("# "):
        md = f"# {title}\n\n{md}"
    return re.sub(r"\n{3,}", "\n\n", md).rstrip() + "\n"


def build_sidebar(index_soup: BeautifulSoup) -> str:
    container = index_soup.select_one("aside nav ul.nav")
    if not isinstance(container, Tag):
        return ""

    lines: list[str] = []

    for li in container.find_all("li", class_="nav-item", recursive=False):
        a = li.find("a", class_="nav-link", recursive=False)
        if not isinstance(a, Tag):
            continue

        href = a.get("href", "")
        p = a.find("p")
        text = p.get_text(" ", strip=True) if isinstance(p, Tag) else a.get_text(" ", strip=True)
        text = re.sub(r"\s+", " ", text).replace("", "").strip()
        text = text.replace("right fas fa-angle-left", "").strip()
        text = text.replace("fas fa-angle-left", "").strip()

        sub_ul = li.find("ul", class_="nav-treeview", recursive=False)
        if isinstance(sub_ul, Tag):
            lines.append(f"- {text}")
            for sub_li in sub_ul.find_all("li", class_="nav-item", recursive=False):
                sub_a = sub_li.find("a", class_="nav-link")
                if not isinstance(sub_a, Tag):
                    continue
                sub_href = sub_a.get("href", "")
                slug = normalize_slug(sub_href)
                if not slug:
                    continue
                sub_p = sub_a.find("p")
                sub_text = (
                    sub_p.get_text(" ", strip=True)
                    if isinstance(sub_p, Tag)
                    else sub_a.get_text(" ", strip=True)
                )
                sub_text = re.sub(r"\s+", " ", sub_text).strip()
                lines.append(f"  - [{sub_text}](/zh/{file_name_for_slug(slug)})")
            continue

        slug = normalize_slug(href)
        if not slug:
            continue
        lines.append(f"- [{text}](/zh/{file_name_for_slug(slug)})")

    return "\n".join(lines).strip() + "\n"


def remove_stale_files(target_dir: Path, keep_files: Iterable[str]) -> list[str]:
    keep = set(keep_files)
    removed: list[str] = []
    for path in target_dir.glob("*.md"):
        if path.name == "_sidebar.md":
            continue
        if path.name not in keep:
            path.unlink()
            removed.append(path.name)
    return sorted(removed)


def sync_docs(base_url: str, target_dir: Path, timeout: int, clean: bool, dry_run: bool) -> dict:
    session = requests.Session()
    session.headers.update(
        {
            "User-Agent": "laravel-admin-doc-sync/1.0",
            "Accept-Language": "zh-CN,zh;q=0.9,en;q=0.8",
        }
    )

    index_soup = fetch_soup(session, f"{base_url}/", timeout=timeout)
    slugs = extract_slugs(index_soup)
    if not slugs:
        raise RuntimeError("No doc links found from sidebar")

    target_dir.mkdir(parents=True, exist_ok=True)

    written: list[str] = []
    for slug in slugs:
        url = abs_doc_url(base_url, slug)
        page = fetch_soup(session, url, timeout=timeout)
        title, body_html = extract_body_html(page)
        md = html_to_markdown(title, body_html)
        name = file_name_for_slug(slug)
        if not dry_run:
            (target_dir / name).write_text(md, encoding="utf-8")
        written.append(name)

    sidebar_md = build_sidebar(index_soup)
    if sidebar_md and not dry_run:
        (target_dir / "_sidebar.md").write_text(sidebar_md, encoding="utf-8")

    removed: list[str] = []
    if clean and not dry_run:
        removed = remove_stale_files(target_dir, written)

    manifest = {
        "source": base_url,
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "count": len(written),
        "files": written,
        "removed": removed,
        "sidebar_updated": bool(sidebar_md),
        "clean": clean,
        "dry_run": dry_run,
    }
    if not dry_run:
        (target_dir / ".sync-manifest.json").write_text(
            json.dumps(manifest, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )
    return manifest


def parse_args() -> argparse.Namespace:
    p = argparse.ArgumentParser(description="Sync laravel-admin zh 1.x docs into docs/zh")
    p.add_argument("--base-url", default=DEFAULT_BASE_URL)
    p.add_argument("--target-dir", default=DEFAULT_TARGET_DIR)
    p.add_argument("--timeout", type=int, default=20)
    p.add_argument("--clean", action="store_true", help="Delete local markdown files not in latest docs")
    p.add_argument("--dry-run", action="store_true")
    return p.parse_args()


def main() -> int:
    args = parse_args()
    manifest = sync_docs(
        base_url=args.base_url.rstrip("/"),
        target_dir=Path(args.target_dir),
        timeout=args.timeout,
        clean=args.clean,
        dry_run=args.dry_run,
    )
    print(json.dumps(manifest, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
