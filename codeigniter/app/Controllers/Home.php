<?php

namespace App\Controllers;

use App\Models\UserModel;
use GuzzleHttp\Client;

class Home extends BaseController
{
    protected $session;
    public function __construct()
    {
        // Load session service
        $this->session = \Config\Services::session();
    }
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
        // Check if user exists
        $id = $this->request->getPost('id');
        $uuid = $this->request->getPost('uuid');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $updatedData = [];
        if ($name)
            $updatedData['name'] = $name;
        if ($email)
            $updatedData['email'] = $email;
        $result = $user_model->update($id, $updatedData);
        if ($result) {
            try {
                $client = new Client();
                // Pass the correct id in the request body
                $response = $client->post("http://localhost:3000/api/updateuser", [
                    'json' => [
                        'uuid' => $uuid, // Correctly pass the id as a string or ObjectId
                        'name' => $name,
                        'email' => $email,
                    ]
                ]);
                if ($response->getStatusCode() == 200) {
                    return redirect()->to("/dashboard")->with("success", "user updated successfully");
                } else {
                    return redirect()->to("/dashboard")->with("error", "Failed to update user");
                }
            } catch (\Exception $e) {
                return redirect()->to("/dashboard")->with("error", 'Error while updating the user.' . $e->getMessage());
            }
        } else {
            return redirect()->to('/dashboard')->with('error', 'Failed to update user from the database');
        }
    }


    public function deleteUser($id, $uuid)
    {
        $user_model = new UserModel();

        // Check if user exists
        $user = $user_model->find($id); // Or however you check for the user existence
        if (!$user) {
            return redirect()->to('/dashboard')->with('error', 'User not found');
        }

        // Delete the user from the database
        $result = $user_model->delete($id);

        if ($result) {
            try {
                // External service deletion
                $client = new Client();
                $response = $client->delete("http://localhost:3000/api/deleteuser/$uuid");

                if ($response->getStatusCode() == 200) {
                    return redirect()->to('/dashboard')->with('success', 'User deleted successfully');
                } else {
                    return redirect()->to('/dashboard')->with('error', 'Failed to delete user on external service');
                }
            } catch (\Exception $e) {
                return redirect()->to('/dashboard')->with('error', 'Error while deleting user: ' . $e->getMessage());
            }
        } else {
            return redirect()->to('/dashboard')->with('error', 'Failed to delete user from the database');
        }
    }


    //upload
    public function uploadUser()
    {
        if (!$this->session->has('user')) {
            return redirect()->to('/login');
        } else {
            return view('Upload');
        }
    }

    public function upload()
    {
        $file = $this->request->getfile('csv_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->getTempName();
            $this->lookupcsv($filePath);
        } else {
            return redirect()->to('/userUpload')->with('error', 'File upload failed. Please check the file and try again.');
        }
        return redirect()->to('/dashboard');
    }

    private function lookupcsv($filePath)
    {
        $csv = fopen($filePath, 'r');
        $user_model = new UserModel();
        $header = fgetcsv($csv);
        while (($row = fgetcsv($csv)) !== false) {
            $uuid = $row[1];
            $name = $row[2];
            $email = $row[3];
            $password = password_hash($row[4], PASSWORD_BCRYPT);
            $data = [
                'uuid' => $uuid,
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ];
            $existingUser = $user_model->where('email', $email)->first();
            if ($existingUser) {
                log_message('info', 'user already exists in sql');
            } else {
                $user_model->save($data);
            }
            $this->syncMongoDB($data);
        }
        fclose($csv);
        return redirect()->to('/userUpload')->with('success', 'user succesfully registered and synced with mongodb');
    }
    private function syncMongoDB($data)
    {
        $client = new Client();
        $nodeApiUrl = 'http://localhost:3000/api/register';
        try {
            $response = $client->post($nodeApiUrl, [
                'json' => [
                    'uuid' => $data['uuid'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password']
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                log_message('info', 'user successfully synced with mongodb');
            } else {
                log_message('error', 'failed to sync with mongodb');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error syncing with Mongodb:' . $e->getMessage());
        }
    }
}
