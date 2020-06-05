<?php

namespace CMS\Service;

use CMS\V1\Entity\MailTemplate;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Exception;

/**
* Mail Trigger
*/
class CsecMail
{

    private $entityManager;
    private $transport;
    private $_base_testes;

    function __construct($entityManager, $transport, $base_testes)
    {

        $this->_base_testes = $base_testes;
        $this->entityManager = $entityManager;
        $this->transport = $transport;

        ini_set('sendmail_from', 'crm@csec.com.br');

        if($this->_base_testes) {
            ini_set('sendmail_from', 'estacio.junior@csec.com.br');
        }

    }

    public function sendMail($user, $subject, $template=false, $options=null, $body=false)
    {

        try {

            if (!$template)
                $bodyTemplate = $this->defaultBody($body);
            else{
                $bodyTemplate = $this->getMailTemplate($template, $options);
                $getAttachment = $this->getAttachment($template);
            }

            $html = new MimePart($bodyTemplate);
            $html->type = Mime::TYPE_HTML;
            $html->charset = 'utf-8';
            $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

            if ($getAttachment) {

                $content = file_get_contents(\Base\Module::UPLOAD_DIR.$getAttachment);
                $ext = pathinfo(\Base\Module::UPLOAD_DIR.$getAttachment, PATHINFO_EXTENSION);

                $file = new MimePart($content);
                $file->type = $this->getMimeTypeFromExtension($ext);
                $file->filename = $getAttachment;
                $file->disposition = Mime::DISPOSITION_ATTACHMENT;
                $file->encoding = Mime::ENCODING_BASE64;

            }

            $body = new MimeMessage();

            if (isset($file)) {
                $body->setParts([$html, $file]);
            } else {
                $body->setParts([$html]);
            }

            $message = new Message();

            if(is_array($user)){
                if($this->_base_testes)
                    $message->addTo('estacio.junior@csec.com.br', $user['name']);
                else
                    $message->addTo($user['email'], $user['name']);

            } else {
                if($this->_base_testes)
                    $message->addTo('estacio.junior@csec.com.br', $user->getFirstName());
                else
                    $message->addTo($user->getEmail(), $user->getFirstName());
            }

            $message->setFrom('estacio.junior@csec.com.br', 'WINDEL');
            $message->setSubject("WINDEL - ".$subject);
            $message->setBody($body);
            $message->setEncoding('UTF-8');

            $message->getHeaders()->addHeaderLine('Reply-To', 'estacio.junior@csec.com.br');
            $message->getHeaders()->addHeaderLine('MIME-Version', '1.0');
            $message->getHeaders()->addHeaderLine('X-Mailer', 'CRM Csec');

            $contentTypeHeader = $message->getHeaders()->get('Content-Type');

            if (isset($file)) {
                $contentTypeHeader->setType('multipart/related');
            } else {
                $contentTypeHeader->setType('text/html');
            }

            if (!$message->isValid())
                throw new Exception("Composição do E-mail inválido, por favor verique os dados do usuário.", 1);

            $transport = $this->transport;

            if (!$transport->send($message))
                throw new Exception("Não foi possível enviar o e-mail", 1);

        } catch (Exception $e) {

            echo $e->getMessage();
            return false;
        }

    }

    private function getAttachment($identifier)
    {
        $template = $this->entityManager->getRepository(MailTemplate::class)
                                        ->findBy(['identifier' => $identifier], NULL, 1);

        if ($template[0]->getImage()) {
            return $template[0]->getImage();
        }

        return false;
    }

    private function getMailTemplate($identifier, $options=false)
    {

        $template = $this->entityManager->getRepository(MailTemplate::class)
                                        ->findBy(['identifier' => $identifier], NULL, 1);

        $header = html_entity_decode($template[0]->getHeader(), ENT_COMPAT, 'UTF-8');
        $content = html_entity_decode($template[0]->getContent(), ENT_COMPAT, 'UTF-8');
        $footer = html_entity_decode($template[0]->getFooter(), ENT_COMPAT, 'UTF-8');

        if (isset($options['header']))
            $header = $this->replaceContent('header', $header, $options);
        if (isset($options['content']))
            $content = $this->replaceContent('content', $content, $options);
        if (isset($options['footer']))
            $footer = $this->replaceContent('footer', $footer, $options);

        return $header.$content.$footer;
    }

    private function replaceContent($name, $subject, $options)
    {
        $return = false;

        // if(count($options[$name]) > 1){
        foreach ($options[$name] as $key => $value) {
            $subject = str_replace("{{".$key."}}", $value, $subject);
        }

        $return = $subject;

        // } else {
        //     if (preg_match_all('/{{(.*)}}/', $subject, $matches, PREG_PATTERN_ORDER)) {
        //         foreach ($options[$name] as $key => $value) {
        //             if( $matches[0]) {
        //                 foreach($matches[0] as $match){
        //                     $return = str_replace($match, $value, $subject);
        //                 }
        //             }
        //         }
        //     }
        // }

        return $return;
    }

    private function defaultBody($bodyText)
    {

        return '
        <!DOCTYPE html>
        <html>
            <head>
                <meta content="text/html; charset=UTF-8" http-equiv="content-type">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"></style>
                <style type="text/css">
                    @font-face {
                        font-family: "Roboto";
                        src: url(https://fonts.googleapis.com/css?family=Roboto");
                    }
                    .bodyw{
                        font-family: "Roboto";
                    }
                </style>
            </head>
            <body class="bodyw">
                <div class="col-md-12">
                    <div class="text-left">
                        <h1 style="color:#4D90FE;
                                   font-size:22px;
                                   border-bottom: 2px solid #4D90FE;
                                   padding-left:0;
                                   padding-right:0;
                                   padding-bottom:10px;">

                                    Prezado(a),
                        </h1>
                    </div>
                </div>
                <div class="col-md-12">
                    '.$bodyText.'
                </div>
                <div class="col-md-12">
                    Favor não responder este e-mail, pois a caixa de e-mail de resposta não é verificada.
                </div>
                <div class="col-md-12">
                    <strong>Atenciosamente,<br /> Csec Sistemas</strong>
                </div>
                <div class="col-md-12">

                    <div class="col-md-6">
                        Csec Sistemas Ltda<br />
                        Rua Tupy, 91 , Bairro: Pio X, Caxias do Sul - RS, CEP: 95034-520<br />
                        Fone: (54) 3025-2540 | 0800 600 2220 | E-mail: comercial@csec.com.br<br />
                    </div>
                    <div class="col-md-6">
                        <a href="'.($this->_base_testes==1 ? 'devel2.' : '').'csec.com.br">
                            <img width="150" height="38" src="http://crm.csec.com.br/assets_custom/email/logo.jpg" />
                        </a>
                    </div>

                </div>
            </body>
        </html>';
    }

    private function getMimeTypeFromExtension($extension)
    {
        switch ($extension) {

            case 'jpg':
                return 'image/jpeg';
            break;
            case 'jpeg':
                return 'image/jpeg';
            break;
            case 'png':
                return 'image/png';
            break;
            case 'gif':
                return 'image/gif';
            break;
            case 'pdf':
                return 'application/pdf';
            break;
            case 'csv':
                return 'text/csv';
            break;
            case 'doc':
                return 'application/msword';
            break;
            case 'odt':
                return 'application/vnd.oasis.opendocument.text';
            break;
            case 'ods':
                return 'application/vnd.oasis.opendocument.spreadsheet';
            break;
            default:
                return 'application/pdf';
            break;
        }
    }
}
