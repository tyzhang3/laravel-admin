<?php

use Encore\Admin\Actions\Action;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HandleController;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class DummyHandleForm extends Form
{
    public function handle(Request $request)
    {
        return response()->json([
            'ok' => true,
            'name' => $request->get('name'),
        ]);
    }

    public function validate(Request $request)
    {
        if (!$request->get('name')) {
            return new MessageBag(['name' => ['The name field is required.']]);
        }

        return false;
    }
}

class DummyHandleAction extends Action
{
    public function handle(Request $request)
    {
        return $this->response()->success('action success');
    }
}

class DummyHandleActionThrows extends Action
{
    public function handle(Request $request)
    {
        throw new Exception('action failed');
    }
}

class DummyHandleSelectable
{
    protected $left;
    protected $right;

    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function render()
    {
        return "selectable:{$this->left}-{$this->right}";
    }
}

class DummyHandleRenderable
{
    public function render($key)
    {
        return "renderable:{$key}";
    }
}

class DummyFormWithoutHandle
{
}

class DummyActionWithoutHandle
{
}

class HandleControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testHandleFormSuccess()
    {
        $this->call('POST', '/admin/_handle_form_', [
            '_form_' => DummyHandleForm::class,
            'name' => 'codex',
        ]);

        $this->assertResponseStatus(200);

        $payload = json_decode($this->response->getContent(), true);
        $this->assertTrue($payload['ok']);
        $this->assertSame('codex', $payload['name']);
    }

    public function testHandleFormValidationFailure()
    {
        $this->call('POST', '/admin/_handle_form_', [
            '_form_' => DummyHandleForm::class,
        ]);

        $this->assertResponseStatus(302);
    }

    public function testHandleActionSuccess()
    {
        $this->call('POST', '/admin/_handle_action_', [
            '_action' => DummyHandleAction::class,
        ]);

        $this->assertResponseStatus(200);

        $payload = json_decode($this->response->getContent(), true);
        $this->assertTrue($payload['status']);
        $this->assertSame('success', $payload['toastr']['type']);
        $this->assertSame('action success', $payload['toastr']['content']);
    }

    public function testHandleActionException()
    {
        $this->call('POST', '/admin/_handle_action_', [
            '_action' => DummyHandleActionThrows::class,
        ]);

        $this->assertResponseStatus(200);

        $payload = json_decode($this->response->getContent(), true);
        $this->assertFalse($payload['status']);
        $this->assertSame('error', $payload['toastr']['type']);
        $this->assertSame('action failed', $payload['toastr']['content']);
    }

    public function testHandleSelectableSuccess()
    {
        $this->call('GET', '/admin/_handle_selectable_', [
            'selectable' => DummyHandleSelectable::class,
            'args' => ['left' => 'foo', 'right' => 'bar'],
        ]);

        $this->assertResponseStatus(200);
        $this->assertSame('selectable:foo-bar', $this->response->getContent());
    }

    public function testHandleSelectableFallbackWhenClassMissing()
    {
        $this->call('GET', '/admin/_handle_selectable_', [
            'selectable' => 'Missing_Selectable_Class',
        ]);

        $this->assertResponseStatus(200);
        $this->assertSame('Missing\\Selectable\\Class', $this->response->getContent());
    }

    public function testHandleRenderableSuccess()
    {
        $this->call('GET', '/admin/_handle_renderable_', [
            'renderable' => DummyHandleRenderable::class,
            'key' => 'abc',
        ]);

        $this->assertResponseStatus(200);
        $this->assertSame('renderable:abc', $this->response->getContent());
    }

    public function testHandleRenderableFallbackWhenClassMissing()
    {
        $this->call('GET', '/admin/_handle_renderable_', [
            'renderable' => 'Missing_Renderable_Class',
        ]);

        $this->assertResponseStatus(200);
        $this->assertSame('Missing\\Renderable\\Class', $this->response->getContent());
    }

    public function testResolveFormThrowsWhenFormMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid form request.');

        $this->invokeProtected(new HandleController(), 'resolveForm', new Request());
    }

    public function testResolveFormThrowsWhenFormClassMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Form [Missing\\Form\\Class] does not exist.');

        $this->invokeProtected(new HandleController(), 'resolveForm', new Request([
            '_form_' => 'Missing\\Form\\Class',
        ]));
    }

    public function testResolveFormThrowsWhenHandleMethodMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Form method DummyFormWithoutHandle::handle() does not exist.');

        $this->invokeProtected(new HandleController(), 'resolveForm', new Request([
            '_form_' => DummyFormWithoutHandle::class,
        ]));
    }

    public function testResolveActionInstanceThrowsWhenActionMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid action request.');

        $this->invokeProtected(new HandleController(), 'resolveActionInstance', new Request());
    }

    public function testResolveActionInstanceThrowsWhenActionClassMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Form [Missing\\Action\\Class] does not exist.');

        $this->invokeProtected(new HandleController(), 'resolveActionInstance', new Request([
            '_action' => 'Missing_Action_Class',
        ]));
    }

    public function testResolveActionInstanceThrowsWhenHandleMethodMissing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action method DummyActionWithoutHandle::handle() does not exist.');

        $this->invokeProtected(new HandleController(), 'resolveActionInstance', new Request([
            '_action' => DummyActionWithoutHandle::class,
        ]));
    }

    protected function invokeProtected($instance, $method, ...$arguments)
    {
        $reflection = new ReflectionClass($instance);
        $target = $reflection->getMethod($method);
        $target->setAccessible(true);

        return $target->invoke($instance, ...$arguments);
    }
}
