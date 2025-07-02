<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class CrudController extends AppController {

    public $components = array('Flash', 'Paginator');

    public function index() {
    
        // Configure search conditions
        $conditions = array();
        if (!empty($this->request->query['search'])) {
            $search = $this->request->query['search'];
            $conditions['Crud.name LIKE'] = '%' . $search . '%';
        }
    
        // Configure status filter
        if (!empty($this->request->query['status'])) {
            $status = $this->request->query['status'];
            $conditions['CrudStatus.status'] = $status == 'PENDING' ? NULL : $status;
        }
    
        // Configure pagination
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'contain' => ['CrudStatus'], // Use "contain" instead of manual joins
            'limit' => 10,
            'order' => array('Crud.id' => 'asc')
        );
    
        // Paginate the data
        $cruds = $this->Paginator->paginate('Crud');
        $this->set(compact('cruds'));
    }

    public function print_index() {
        // Import FPDF
        App::import('Vendor', 'FPDF', array('file' => 'fpdf/fpdf.php'));
    
        // Build conditions similar to index()
        $conditions = array();
        if (!empty($this->request->query['search'])) {
            $search = $this->request->query['search'];
            $conditions['Crud.name LIKE'] = '%' . $search . '%';
        }
        if (!empty($this->request->query['status'])) {
            $status = $this->request->query['status'];
            $conditions['CrudStatus.status'] = $status == 'PENDING' ? NULL : $status;
        }
    
        // Fetch all matching CRUD records
        $cruds = $this->Crud->find('all', array(
            'conditions' => $conditions,
            'contain' => array('CrudStatus', 'Beneficiary'),
            'order' => array('Crud.id' => 'asc')
        ));
    
        // Initialize FPDF and create a new page
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
    
        // Title for the PDF page
        $pdf->Cell(0, 10, 'CRUD Records List', 0, 1, 'C');
        $pdf->Ln(5);
    
        // Loop through each record and output its details
        foreach ($cruds as $crud) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Record ID: ' . $crud['Crud']['id'], 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, 'Name: ' . $crud['Crud']['name'], 0, 1);
            $pdf->Cell(0, 8, 'Email: ' . $crud['Crud']['email'], 0, 1);
            $pdf->Cell(0, 8, 'Birth Date: ' . $crud['Crud']['birth_date'], 0, 1);
            $pdf->Cell(0, 8, 'Status: ' . ($crud['CrudStatus']['status'] ?? 'PENDING'), 0, 1);
            $pdf->Ln(3);
    
            // Beneficiaries
            if (!empty($crud['Beneficiary'])) {
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, 'Beneficiaries:', 0, 1);
                $pdf->SetFont('Arial', '', 12);
                foreach ($crud['Beneficiary'] as $beneficiary) {
                    $pdf->Cell(0, 8, ' - Name: ' . $beneficiary['name'] . ', Birth Date: ' . $beneficiary['birth_date'] . ', Relationship: ' . $beneficiary['relationship'], 0, 1);
                }
            }
            $pdf->Ln(10); // space between records
        }
    
        // Output the PDF directly
        $pdf->Output();
        exit;
    }    
    
    public function add() {
        if ($this->request->is('post')) {
            $this->Crud->create();
            if ($this->Crud->save($this->request->data)) {
                $crudId = $this->Crud->getLastInsertId();

                // Handle file uploads
                if (!empty($this->request->data['Crud']['files'])) {
                    foreach ($this->request->data['Crud']['files'] as $file) {
                        if ($file['error'] == 0) {
                            $filePath = 'uploads/' . time() . '_' . $file['name'];
                            move_uploaded_file($file['tmp_name'], WWW_ROOT . $filePath);
                            $this->Crud->CrudFile->create();
                            $this->Crud->CrudFile->save(array(
                                'crud_id' => $crudId,
                                'file_name' => $file['name'],
                                'file_path' => $filePath
                            ));
                        }
                    }
                }

                // Check if beneficiaries exist in the request
                if (!empty($this->request->data['beneficiaries'])) {
                    foreach ($this->request->data['beneficiaries'] as &$beneficiary) {
                        $beneficiary['cruds_id'] = $crudId; // Use the correct foreign key
                    }

                    // Load Beneficiary model and save the data
                    $this->loadModel('Beneficiary');
                    if (!$this->Beneficiary->saveAll($this->request->data['beneficiaries'])) {
                        $this->Flash->error('Failed to save beneficiaries');
                    }
                }
                $to = 'editedweare@gmail.com';
                $subject = 'Your record has been created';
                $message = "Hello,\n\nYour record '{$this->request->data['Crud']['name']}' has been created.\n\nRegards,\nAdmin";
            
                if ($this->sendEmailNotification($to, $subject, $message)) {
                    $this->Flash->success(__('The record has been saved and email sent'));
                } else {
                    $this->Flash->success(__('The record has been saved but email failed'));
                }
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Unable to add record'));
            }
        }
    }

    public function edit($id = null) {
        if (!$id || !$crud = $this->Crud->findById($id)) {
            throw new NotFoundException(__('Invalid record'));
        }

        // Load related CrudFile and Beneficiary data
        $this->loadModel('CrudFile');
        $this->loadModel('Beneficiary');

        // Fetch files and beneficiaries
        $files = $this->CrudFile->find('all', [
            'conditions' => ['CrudFile.crud_id' => $id]
        ]);
        $beneficiaries = $this->Beneficiary->find('all', [
            'conditions' => ['Beneficiary.cruds_id' => $id]
        ]);

        if ($this->request->is(['post', 'put'])) {
            $this->Crud->id = $id;
            if ($this->Crud->save($this->request->data)) {
                // Handle file uploads
                if (!empty($this->request->data['Crud']['files'])) {
                    foreach ($this->request->data['Crud']['files'] as $file) {
                        if ($file['error'] == 0) {
                            $filePath = 'uploads/' . time() . '_' . $file['name'];
                            move_uploaded_file($file['tmp_name'], WWW_ROOT . $filePath);
                            $this->Crud->CrudFile->create();
                            $this->Crud->CrudFile->save(array(
                                'crud_id' => $id,
                                'file_name' => $file['name'],
                                'file_path' => $filePath
                            ));
                        }
                    }
                }

                // Process file deletions
                if (!empty($this->request->data['CrudFile'])) {
                    foreach ($this->request->data['CrudFile'] as $fileId => $fileData) {
                        if (!empty($fileData['delete']) && $fileData['delete'] == '1') {
                            // Find the file record
                            $file = $this->CrudFile->findById($fileId);
                            if ($file) {
                                // Delete the file from the filesystem
                                $filePath = WWW_ROOT . $file['CrudFile']['file_path'];
                                if (file_exists($filePath)) {
                                    unlink($filePath); // Delete the physical file
                                }

                                // Delete the file record from the database
                                $this->CrudFile->delete($fileId);
                            }
                        }
                    }
                }

                // Process beneficiaries
                if (!empty($this->request->data['beneficiaries'])) {
                    foreach ($this->request->data['beneficiaries'] as $beneficiary) {
                        if (!empty($beneficiary['delete']) && $beneficiary['delete'] == '1') {
                            // Delete the beneficiary
                            $this->Beneficiary->delete($beneficiary['id']);
                        } else {
                            // Update or add beneficiary
                            if (!empty($beneficiary['id'])) {
                                $this->Beneficiary->id = $beneficiary['id'];
                            } else {
                                $this->Beneficiary->create();
                                $beneficiary['cruds_id'] = $id;
                            }
                            $this->Beneficiary->save($beneficiary);
                        }
                    }
                }

                $this->Flash->success(__('Record updated successfully'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update record'));
        }

        // Pass data to the view
        $this->request->data = $crud;
        $this->set(compact('files', 'beneficiaries'));
    }

    public function delete_beneficiary($id = null) {
        $this->autoRender = false;
        $this->loadModel('Beneficiary');

        if (!$id || !$this->Beneficiary->exists($id)) {
            echo json_encode(['success' => false]);
            return;
        }

        if ($this->Beneficiary->delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid record'));
        }

        // Fetch the CRUD record with related data
        $crud = $this->Crud->find('first', array(
            'conditions' => array('Crud.id' => $id),
            'contain' => ['CrudStatus', 'Beneficiary', 'CrudFile'] // Use "contain" to fetch related data
        ));

        if (!$crud) {
            throw new NotFoundException(__('Record not found'));
        }

        $this->set(compact('crud'));
    }

    public function approve($id) {
        $this->autoRender = false;
        $this->loadModel('CrudStatus');
    
        // Fetch the CRUD record to get the name
        $crud = $this->Crud->findById($id);
    
        if (!$crud) {
            throw new NotFoundException(__('Record not found'));
        }
    
        // Check if a status already exists for this CRUD record
        $status = $this->CrudStatus->findById($id);
    
        if ($status) {
            // Update existing status
            $this->CrudStatus->id = $id;
            $this->CrudStatus->save(array(
                'name' => $crud['Crud']['name'],
                'status' => 'APPROVED'
            ));
        } else {
            // Create new status with the same ID and name as the CRUD record
            $this->CrudStatus->create();
            $this->CrudStatus->save(array(
                'id' => $id, // Match cruds.id
                'name' => $crud['Crud']['name'],
                'status' => 'APPROVED'
            ));
        }
    
        // Send email notification
        $to = 'editedweare@gmail.com'; // Replace with recipient email
        $subject = 'Your record has been approved';
        $message = "Hello,\n\nYour record '{$crud['Crud']['name']}' has been approved.\n\nRegards,\nAdmin";
    
        if ($this->sendEmailNotification($to, $subject, $message)) {
            $this->Flash->success('Record approved and email sent');
        } else {
            $this->Flash->success('Record approved but email failed');
        }
    
        $this->redirect(['action' => 'index']);
    }
    
    public function disapprove($id) {
        $this->autoRender = false;
        $this->loadModel('CrudStatus');
    
        // Fetch the CRUD record to get the name
        $crud = $this->Crud->findById($id);
    
        if (!$crud) {
            throw new NotFoundException(__('Record not found'));
        }
    
        // Check if a status already exists for this CRUD record
        $status = $this->CrudStatus->findById($id);
    
        if ($status) {
            // Update existing status
            $this->CrudStatus->id = $id;
            $this->CrudStatus->save(array(
                'name' => $crud['Crud']['name'],
                'status' => 'DISAPPROVED'
            ));
        } else {
            // Create new status with the same ID and name as the CRUD record
            $this->CrudStatus->create();
            $this->CrudStatus->save(array(
                'id' => $id, // Match cruds.id
                'name' => $crud['Crud']['name'],
                'status' => 'DISAPPROVED'
            ));
        }
    
        // Send email notification
        $to = 'editedweare@gmail.com'; // Replace with recipient email
        $subject = 'Your record has been disapproved';
        $message = "Hello,\n\nYour record '{$crud['Crud']['name']}' has been disapproved.\n\nRegards,\nAdmin";
    
        if ($this->sendEmailNotification($to, $subject, $message)) {
            $this->Flash->error('Record disapproved and email sent');
        } else {
            $this->Flash->error('Record disapproved but email failed');
        }
    
        $this->redirect(['action' => 'index']);
    }

    public function delete($id = null) {
        $this->autoRender = false; // Disable view rendering for AJAX requests
    
        if (!$this->request->is('post')) {
            return json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    
        $this->loadModel('Crud');
        $this->loadModel('Beneficiary');
        $this->loadModel('CrudFile');
    
        // Find the main record
        $crud = $this->Crud->findById($id);
    
        if (!$crud) {
            return json_encode(['success' => false, 'message' => 'Record not found']);
        }
    
        // Delete related beneficiaries
        $this->Beneficiary->deleteAll(['Beneficiary.cruds_id' => $id]);
    
        // Delete associated files
        $files = $this->CrudFile->find('all', ['conditions' => ['CrudFile.crud_id' => $id]]);
        foreach ($files as $file) {
            $filePath = WWW_ROOT . '/' . $file['CrudFile']['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the physical file
            }
            $this->CrudFile->delete($file['CrudFile']['id']);
        }
    
        // Delete the main record
        if ($this->Crud->delete($id)) {
            $this->Flash->success('Record deleted');
        } else {
            $this->Flash->error('Failed to delete the record');
        }
        $this->redirect(['action' => 'index']);
    }
    
    public function print($id) {
        App::import('Vendor', 'FPDF', array('file' => 'fpdf/fpdf.php'));
    
        $crud = $this->Crud->find('first', [
            'conditions' => ['Crud.id' => $id],
            'contain' => ['CrudStatus', 'Beneficiary']
        ]);
    
        if (!$crud) {
            throw new NotFoundException(__('Record not found'));
        }
    
        // Initialize FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
    
        // Text
        $pdf->Cell(0, 10, 'CRUD Record', 0, 1, 'C');
    
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'General Information', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Name: ' . $crud['Crud']['name']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Email: ' . $crud['Crud']['email']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Birth Date: ' . $crud['Crud']['birth_date']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Status: ' . $crud['CrudStatus']['status']);
        $pdf->Ln(20);
        
        if (!empty($crud['Beneficiary'])) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Beneficiaries:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            foreach ($crud['Beneficiary'] as $beneficiary) {
                $pdf->Cell(40, 10, 'Name: ' . $beneficiary['name']);
                $pdf->Ln();
                $pdf->Cell(40, 10, 'Birth Date: ' . $beneficiary['birth_date']);
                $pdf->Ln();
                $pdf->Cell(40, 10, 'Relationship: ' . $beneficiary['relationship']);
                $pdf->Ln(15);
            }
        }
    
        $pdf->Output();
        exit;
    }
    
    private function sendEmailNotification($to, $subject, $message) {
        $email = new CakeEmail('gmail'); // Uses the 'gmail' config from email.php
        try {
            $email->to($to)
                ->subject($subject)
                ->emailFormat('both') // Sends both HTML & plain text
                ->send($message);
            return true; // Email sent successfully
        } catch (Exception $e) {
            return false; // Email failed
        }
    }
    
}