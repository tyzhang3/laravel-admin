if ($.fn.editable) {
    $.fn.editable.defaults.params = function (params) {
        params._token = LA.token;
        params._editable = 1;
        params._method = 'PUT';
        return params;
    };

    $.fn.editable.defaults.error = function (data) {
        var msg = '';
        if (data.responseJSON.errors) {
            $.each(data.responseJSON.errors, function (k, v) {
                msg += v + "\n";
            });
        }
        return msg;
    };
}

toastr.options = {
    closeButton: true,
    progressBar: true,
    showMethod: 'slideDown',
    timeOut: 4000
};

if ($.pjax && $.pjax.defaults) {
    $.pjax.defaults.timeout = 5000;
    $.pjax.defaults.maxCacheLength = 0;
    $(document).pjax('a:not(a[target="_blank"])', {
        container: '#pjax-container'
    });

    NProgress.configure({parent: '#app'});

    $(document).on('pjax:timeout', function (event) {
        event.preventDefault();
    })

    $(document).on('submit', 'form[pjax-container]', function (event) {
        $.pjax.submit(event, '#pjax-container')
    });

    $(document).on("pjax:popstate", function () {
        $(document).one("pjax:end", function (event) {
            $(event.target).find("script[data-exec-on-popstate]").each(function () {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        });
    });

    $(document).on('pjax:send', function (xhr) {
        NProgress.start();
    });

    $(document).on('pjax:complete', function (xhr) {
        NProgress.done();
        $.admin.grid.selects = {};
    });
}

$(document).click(function () {
    $('.sidebar .dropdown-menu').hide();
});

$(function () {
    $('.sidebar-menu li:not(.has-treeview) > a').on('click', function () {
        var $link = $(this).addClass('active');
        var $parent = $link.parent();
        $parent.siblings('.has-treeview.menu-open').find('> a').removeClass('active');
        $parent.siblings().removeClass('active').find('li').removeClass('active');
    });
    var menu = $('.sidebar-menu li > a[href$="' + (location.pathname + location.search + location.hash) + '"]');
    menu.addClass('active');
    menu.parent().addClass('active');
    menu.parents('ul.nav-treeview').siblings('a').addClass('active');
    menu.parents('li.has-treeview').addClass('menu-open');

    $('[data-toggle="popover"]').popover();

    // Sidebar form autocomplete
    $('.sidebar .autocomplete').on('keyup focus', function () {
        var $menu = $(this).closest('.input-group').find('.dropdown-menu');
        var text = $(this).val();

        if (text === '') {
            $menu.hide();
            return;
        }

        var regex = new RegExp(text, 'i');
        var matched = false;

        $menu.find('li').each(function () {
            if (!regex.test($(this).find('a').text())) {
                $(this).hide();
            } else {
                $(this).show();
                matched = true;
            }
        });

        if (matched) {
            $menu.show();
        }
    }).click(function(event){
        event.stopPropagation();
    });

    $('.sidebar .dropdown-menu li a').click(function (){
        $(this).closest('.input-group').find('.autocomplete').val($(this).text().trim());
    });
});

$(window).scroll(function() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        $('#totop').fadeIn(500);
    } else {
        $('#totop').fadeOut(500);
    }
});

$('#totop').on('click', function (e) {
    e.preventDefault();
    $('html,body').animate({scrollTop: 0}, 500);
});

(function ($) {

    var Grid = function () {
        this.selects = {};
    };

    Grid.prototype.select = function (id) {
        this.selects[id] = id;
    };

    Grid.prototype.unselect = function (id) {
        delete this.selects[id];
    };

    Grid.prototype.selected = function () {
        var rows = [];
        $.each(this.selects, function (key, val) {
            rows.push(key);
        });

        return rows;
    };

    $.fn.admin = LA;
    $.admin = LA;
    $.admin.swal = swal;
    $.admin.toastr = toastr;
    $.admin.grid = new Grid();

    $.admin.reload = function () {
        if ($.pjax) {
            $.pjax.reload('#pjax-container');
        } else {
            window.location.reload();
        }
        $.admin.grid = new Grid();
    };

    $.admin.redirect = function (url) {
        if ($.pjax) {
            $.pjax({container:'#pjax-container', url: url });
        } else {
            window.location.href = url;
        }
        $.admin.grid = new Grid();
    };

    $.admin.getToken = function () {
        return $('meta[name="csrf-token"]').attr('content');
    };

    $.admin.loadedScripts = [];

    $.admin.loadScripts = function(arr) {
        var _arr = $.map(arr, function(src) {

            if ($.inArray(src, $.admin.loadedScripts) !== -1) {
                return;
            }

            $.admin.loadedScripts.push(src);

            return $.getScript(src);
        });

        _arr.push($.Deferred(function(deferred){
            $(deferred.resolve);
        }));

        return $.when.apply($, _arr);
    }

})(jQuery);
