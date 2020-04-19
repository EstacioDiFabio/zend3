<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\Form\UserForm;
use CMS\V1\Entity\User;
use Auth\V1\Entity\Role;
use CMS\V1\Entity\Job;
use CMS\V1\Entity\WorkGroup;
use CMS\V1\Entity\Departament;
use CMS\V1\Entity\Organization;
use CMS\Form\PasswordChangeForm;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's password).
 */
class UserController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'firstName' => 'Nome',
        'lastName' => 'Sobrenome',
        'email' => 'E-mail',
        'dateCreated' => 'Data de Criação',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of users.
     */
    public function indexAction()
    {
        $users = $this->entityManager->getRepository(User::class)
                      ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'users' => $users,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods
        ]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        $qb = $this->entityManager->createQueryBuilder();
        $alias = "u";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $users = $qb->select($alias)
                   ->from(User::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($users->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {

            foreach ($users->getResult() as $key => $user) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('usuarios', $user->getId(), $user->getEmail(), 'Visualizar'),
                    '1' => $user->getFirstName()." ".$user->getLastName(),
                    '2' => $user->getRolesAsString(),
                    '3' => $user->getIdJob() ? $user->getJobAsString() : "",
                    '4' => $user->getDateCreated()->format('d/m/Y'),
                    '5' => $user->getStatusToggle(),
                    '6' => $this->windelHtml()->getActionButton('usuarios', $user->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * The "getDependeciesEntity" action is used to return a list of all relations
     * on user's table.
     * @return [array] [list of relations]
     */
    private function getDependeciesEntity()
    {
        $return = array();

        // Get the list of all available roles (sorted by name).
        $allRoles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['name'=>'ASC']);

        $roleList = [];

        foreach ($allRoles as $role) {
            $roleList['roles'][$role->getId()] = $role->getName();
        }
        array_push($return, $roleList);

        // Get the list of all available jobs (sorted by name).
        $allJobs = $this->entityManager->getRepository(Job::class)
                ->findBy([], ['name'=>'ASC']);

        $jobList = [];

        foreach ($allJobs as $job) {
            $jobList['jobs'][$job->getId()] = $job->getName();
        }

        array_push($return, $jobList);

        // Get the list of all available workGroup (sorted by name).
        $allWorkGroups = $this->entityManager->getRepository(WorkGroup::class)
                ->findBy([], ['name'=>'ASC']);

        $workGroupList['work_groups'] = [];

        foreach ($allWorkGroups as $group) {
            $workGroupList['work_groups'][$group->getId()] = $group->getName();
        }

        array_push($return, $workGroupList);

        // Get the list of all available departaments (sorted by name).
        $allDepartaments = $this->entityManager->getRepository(Departament::class)
                ->findBy([], ['name'=>'ASC']);

        $departamentList['departaments'] = [];

        foreach ($allDepartaments as $departament) {
            $departamentList['departaments'][$departament->getId()] = $departament->getName();
        }

        array_push($return, $departamentList);

        // Get the list of all available organizations (sorted by name).
        $allOrganizations = $this->entityManager->getRepository(Organization::class)
                ->findBy([], ['name'=>'ASC']);

        $organizationList['organizations'] = [];

        foreach ($allOrganizations as $organization) {
            $organizationList['organizations'][$organization->getId()] = $organization->getName();
        }

        array_push($return, $organizationList);

        return $return;
    }

    /**
     * This action displays a page allowing to add a new user.
     */
    public function addAction()
    {
        // Create user form
        $form = new UserForm('create', $this->entityManager);

        $dependenciesList = $this->getDependeciesEntity();

        $roleList = $dependenciesList[0]['roles'];
        $jobList = $dependenciesList[1]['jobs'];
        $workGroupList = $dependenciesList[2]['work_groups'];
        $departamentList = $dependenciesList[3]['departaments'];
        $organizationList = $dependenciesList[4]['organizations'];

        $form->get('roles')->setValueOptions($roleList);
        $form->get('job')->setValueOptions($jobList);
        $form->get('work_groups')->setValueOptions($workGroupList);
        $form->get('departaments')->setValueOptions($departamentList);
        $form->get('organizations')->setValueOptions($organizationList);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add user.
                $user = $this->userManager->addUser($data);

                if(is_string($user)){
                    $this->flashMessenger()->addErrorMessage($user);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Usuário criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'view', 'id'=>$user->getId()]);
                }

            } else {
                echo "<pre>"; die(var_dump($form->getMessages()));
            }
        }

        return new ViewModel([
                'form' => $form
            ]);
    }

    /**
     * The "view" action displays a page allowing to view user's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new UserForm('update', $this->entityManager, $user);

        $dependenciesList = $this->getDependeciesEntity();

        $roleList = $dependenciesList[0]['roles'];
        $jobList = $dependenciesList[1]['jobs'];
        $workGroupList = $dependenciesList[2]['work_groups'];
        $departamentList = $dependenciesList[3]['departaments'];
        $organizationList = $dependenciesList[4]['organizations'];

        $form->get('roles')->setValueOptions($roleList);
        $form->get('job')->setValueOptions($jobList);
        $form->get('work_groups')->setValueOptions($workGroupList);
        $form->get('departaments')->setValueOptions($departamentList);
        $form->get('organizations')->setValueOptions($organizationList);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the user.
                $user = $this->userManager->updateUser($user, $data);

                if(is_string($user)){
                    $this->flashMessenger()->addErrorMessage($user);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Usuário alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'view', 'id'=>$user->getId()]);
                }

            }
        } else {

            $userRoleIds = [];
            foreach ($user->getRoles() as $role) {
                $userRoleIds[] = $role->getId();
            }
            $userWorkGroupIds = [];
            foreach ($user->getWorkGroup() as $group) {
                $userWorkGroupIds[] = $group->getId();
            }
            $userDepartamentIds = [];
            foreach ($user->getDepartament() as $departament) {
                $userDepartamentIds[] = $departament->getId();
            }
            $userOrganizationIds = [];
            foreach ($user->getOrganization() as $organization) {
                $userOrganizationIds[] = $organization->getId();
            }

            $form->setData(array(
                    'first_name'=>$user->getFirstName(),
                    'last_name'=>$user->getLastName(),
                    'email'=>$user->getEmail(),
                    'user_id_crm'=>$user->getUserIdCrm(),
                    'roles' => $userRoleIds,
                    'status'=>$user->getStatus(),
                    'job' => $user->getIdJob() ? $user->getIdJob()->getId() : "",
                    'work_groups' => $userWorkGroupIds,
                    'departaments' => $userDepartamentIds,
                    'organizations' => $userOrganizationIds,
            ));
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $user = $this->entityManager->getRepository(User::class)->find($data['id']);

            if ($user == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $user = $this->userManager->patchUser($user, $data);

            if(is_string($user)){
                $this->flashMessenger()->addErrorMessage($user);
            } else {
                $this->flashMessenger()->addSuccessMessage("Usuário alterado com sucesso!");
            }

            return true;
        }
    }

     /**
     * This action remove a user data.
     */

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $user = $this->entityManager->getRepository(User::class)
                    ->findOneById($id);

        if ($user == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $user = $this->userManager->removeUser($user);

        if(is_string($user)){
            $this->flashMessenger()->addErrorMessage($user);
        } else {
            $this->flashMessenger()->addSuccessMessage("Usuário removido com sucesso!");
            // Redirect the user to "index" page.
            return $this->redirect()->toRoute('users', ['action'=>'index']);
        }

    }

    /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
                ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create "change password" form
        $form = new PasswordChangeForm('change');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Try to change password.
                if (!$this->userManager->changePassword($user, $data)) {
                    $this->flashMessenger()->addErrorMessage(
                            'Desculpe, a senha atual está incorreta. Não foi possível alterar a nova senha.');
                } else {
                    $this->flashMessenger()->addSuccessMessage(
                            'Senha alterada com sucesso.');
                }

                // Redirect to "view" page
                return $this->redirect()->toRoute('users',
                        ['action'=>'view', 'id'=>$user->getId()]);
            }
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * This action displays an informational message page.
     * For example "Your password has been resetted" and so on.
     */
    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }

        return new ViewModel([
            'id' => $id
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }

        if($token===null ||
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('users',
                    ['action'=>'message', 'id'=>'failed']);
        }

        // Create form
        $form = new PasswordChangeForm('reset');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                $data = $form->getData();

                // Set new password for the user.
                if ($this->userManager->setNewPasswordByToken($token, $data['new_password'])) {

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'set']);
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                            ['action'=>'message', 'id'=>'failed']);
                }
            }
        }
        $this->layout()->setTemplate('layout/layout2');
        return new ViewModel([
            'form' => $form
        ]);
    }
}


