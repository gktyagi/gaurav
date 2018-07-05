<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
	var $uses = array('Images');
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function login()
	{
		if ($this->request->is('post')) {
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);
				return $this->redirect(['controller'=>'users','action'=>'uploadImages']);
			}
			$this->Flash->error(__('Invalid user name and password.'));
		}
	}
	
	public function logout()
	{
		return $this->redirect($this->Auth->logout());
	}
	
	public function uploadImages()
	{
		if ($this->request->is('post')) {
			// create image folder if not exist
			$filename = 'img/upload_pics/';
			if (!file_exists($filename)) {
				mkdir($filename, 0777, true);
			}
			// create thumb image folder if not exist
			$thumbImageFolder = 'img/upload_pics_thumb/';
			if (!file_exists($thumbImageFolder)) {
				mkdir($thumbImageFolder, 0777, true);
			}
			// table register
			$images_table = TableRegistry::get('images');	
			
			$uid = $this->Auth->user('id'); // current session user id
			
			foreach($this->request->data['images'] as $imageData){
				if(!isset($imageData['name'])){
					continue;
				}
				$info = $images_table->newEntity();
				
				$info->image_name = $imageData['name'];
				$info->user_id = $uid;
				
				if ($images_table->save($info)) {
					copy($imageData['tmp_name'],$thumbImageFolder.$imageData['name']);
					move_uploaded_file($imageData['tmp_name'],$filename.$imageData['name']); // upload image into folder
					$this->__generateThumbnail($imageData,'C:\xampp\htdocs\gaurav\webroot\img\upload_pics_thumb/'.$imageData['name']);
				}
			}
			
			echo "saved successfully";
			exit;
		}
	}
	
	private function __generateThumbnail($img,$upload_image)
	{
		//thumbnail creation
		$file_ext = explode('.',$img['tmp_name']);
		list($width,$height) = getimagesize($upload_image);
		$thumb_create = imagecreatetruecolor(100,100);
		switch($file_ext){
			case 'jpg':
				$source = imagecreatefromjpeg($upload_image);
				break;
			case 'jpeg':
				$source = imagecreatefromjpeg($upload_image);
				break;

			case 'png':
				$source = imagecreatefrompng($upload_image);
				break;
			case 'gif':
				$source = imagecreatefromgif($upload_image);
				break;
			default:
				$source = imagecreatefromjpeg($upload_image);
		}

		imagecopyresized($thumb_create,$source,0,0,0,0,100,100,100,100);
		switch($file_ext){
			case 'jpg' || 'jpeg':
				imagejpeg($thumb_create,$upload_image,100);
				break;
			case 'png':
				imagepng($thumb_create,$upload_image,100);
				break;

			case 'gif':
				imagegif($thumb_create,$upload_image,100);
				break;
			default:
				imagejpeg($thumb_create,$upload_image,100);
		}		
	}
}
