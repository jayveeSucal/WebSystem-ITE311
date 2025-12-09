<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Controllers\Admin;
use App\Models\UserModel;

/**
 * @internal
 */
final class AdminControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock session
        $this->session = \Config\Services::session();
        \CodeIgniter\Config\Services::injectMock('session', $this->session);
    }

    public function testAdminCannotDeactivateSelf()
    {
        // Mock the session to simulate an admin user
        $this->session->set([
            'isLoggedIn' => true,
            'userId' => 1,
            'userRole' => 'admin',
            'userName' => 'Admin User',
            'userEmail' => 'admin@example.com',
        ]);

        // Create a mock user model
        $userModel = $this->createMock(UserModel::class);
        $userModel->method('find')->willReturn([
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'active' => 1,
        ]);

        // Mock the controller
        $controller = new Admin();
        $controller->userModel = $userModel;

        // Simulate the request
        $result = $controller->toggleUserStatus(1);

        // Assert that it redirects with an error message
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals('You cannot deactivate your own admin account.', session('user_error'));
    }

    public function testAdminCanDeactivateOtherUser()
    {
        // Mock the session to simulate an admin user
        $this->session->set([
            'isLoggedIn' => true,
            'userId' => 1,
            'userRole' => 'admin',
            'userName' => 'Admin User',
            'userEmail' => 'admin@example.com',
        ]);

        // Create a mock user model
        $userModel = $this->createMock(UserModel::class);
        $userModel->method('find')->willReturn([
            'id' => 2,
            'name' => 'Other User',
            'email' => 'other@example.com',
            'role' => 'student',
            'active' => 1,
        ]);
        $userModel->expects($this->once())->method('update')->with(2, ['active' => 0]);

        // Mock the controller
        $controller = new Admin();
        $controller->userModel = $userModel;

        // Simulate the request
        $result = $controller->toggleUserStatus(2);

        // Assert that it redirects with a success message
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals('User has been deactivated.', session('user_success'));
    }
}
