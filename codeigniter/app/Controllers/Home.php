<?php

namespace App\Controllers;

use App\Models\UserModel;
use GuzzleHttp\Client;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function dashboard()
    {
        if (!$this->session->has('user')) {
            return redirect()->to('/login');
        }
        $user_model = new UserModel();
        $users = $user_model->findAll();
        return view('dashboard', ['users' => $users]);
    }


    public function signup()
    {
        if ($this->session->has('user')) {
            return redirect()->to('/dashboard');
        }
        if (isset($_POST['name'])) {
            $user_model = new UserModel();
            $data = [
                'uuid' => $this->request->getpost('uuid'),
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT)
            ];
            $result = $user_model->save($data);
            if ($result) {
                $client = new Client();
                $nodeApiUrl = 'http://localhost:3000/api/register';
                try {
                    $response = $client->post($nodeApiUrl, [
                        'json' => [
                            'uuid' => $data['uuid'],
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT)
                        ]
                    ]);
                    if ($response) {
                        return redirect()->to('/login')->with('success', 'Registration successful! Please log in.');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('Error connecting to node.js server', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', 'Failed to register. Please try again.');
            }
        }
        return view('signup');
    }

    public function login()
    {
        if ($this->session->has('user')) {
            return redirect()->to('/dashboard');
        }
        if (isset($_POST['email'])) {
            $user_model = new UserModel();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user = $user_model->where('email', $email)->first();
            if ($user) {
                if (password_verify($password, $user->password)) {
                    $this->session->set("user", $user);
                    return redirect()->to('/dashboard')->with('success', 'Login successful!');
                } else {
                    return redirect()->back()->with('error', 'Invalid password. Please try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid email. Please try again.');
            }
        }
        return view('login');
    }



    public function logout()
    {
        $this->session->remove('user');
        $this->session->setFlashdata('success', 'You have logged out successfully.');
        return redirect()->to('/login');
    }


    public function updateUser()
    {
        $user_model = new UserModel();

        // Get the submitted data
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');

        // Prepare data for update
        $updatedData = [];
        if ($name)
            $updatedData['name'] = $name;
        if ($email)
            $updatedData['email'] = $email;

        // Update the user in the database
        $user_model->update($id, $updatedData);

        // Redirect to the dashboard with a success message
        return redirect()->to('/dashboard')->with('success', 'User updated successfully!');
    }

    public function deleteUser($id)
    {
        $user_model = new UserModel();

        // Delete the user from the database
        $user_model->delete($id);

        // Redirect to the dashboard with a success message
        return redirect()->to('/dashboard')->with('success', 'User deleted successfully!');
    }
}