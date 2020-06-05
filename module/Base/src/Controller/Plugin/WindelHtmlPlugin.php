<?php
namespace Base\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * This controller plugin is used for role-based access control (RBAC).
 */
class CsecHtmlPlugin extends AbstractPlugin
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Authentication service.
     * @var Zend\Authentication\AuthenticationService
     */
    private $authService;


    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    public function getHref($controller, $action, $id=false)
    {
        $link ="href=".$controller."/".$action."/".$id ?? "";
        return $link;
    }

    public function getActionButton($controller, $id)
    {
        $href = $this->getHref($controller, "edit", $id);
        $hrefPass = $this->getHref($controller, "change-password", $id);

        $edit = "<a class='btn btn-sm btn-info hoverable-scale' {$href} title='Editar'> <i class='far fa-edit'></i> </a>";

        $remove = '<a class="btn btn-sm btn-danger delete-action hoverable-scale" data-id="{$id}" href="#" title="Remover">
                      <i class="fa fa-trash"></i>
                   </a>';

        $senha = '<a class="btn btn-warning btn-sm hoverable-scale" {$hrefPass} title="Alterar Senha">
                    <i class="fas fa-asterisk"></i>
                  </a>';

        $finalizar = '<a class="btn btn-sm btn-warning hoverable-scale" {$href} title="Finalizar">
                        <i class="fas fa-check"></i>
                      </a>';

        $padrao = $edit." ".$remove;
        $usuario = $edit." ".$remove." ".$senha;
        $agendamento = $finalizar;

        if($controller == 'usuarios')
            return $usuario;
        else if($controller == 'agendamento')
            return $agendamento;
        else
            return $padrao;
    }

    public function getLink($controller, $id, $name, $title)
    {
        $href = $this->getHref($controller, 'view', $id);

        return '<a '.$href.'
                   title="'.$title.'">'.$name.'</a>';
    }
}