<?php
declare(strict_types=1);

namespace Application\Model;

use Zend\Log\Logger;
class Log extends Logger
{

    private $_rootFolder;

    public function __construct($tipo)
    {
        parent::__construct();

        // Define o local do log
        $path = APPLICATION_DATA . '/logs/api/'. $tipo . date('/Y/m');

        if ( !file_exists($path)) {
            if ( @mkdir($path, 0777, true) ) {
                $path = realpath($path);
            }
        } else {
            $path = realpath($path);
        }

        // Define o nome do arquivo de log
        $filename = $path .'/' . date('Y-m-d_H') . '.log';

        // Verifica se o arquivo existe e cria ele com as permissões corretas
        if (file_exists($filename) && is_writeable($filename)) {
            chmod($filename, 0766);
        } elseif (!file_exists($filename)) {
            $fp = fopen($filename, 'w');
            fclose($fp);
            chmod($filename, 0766);
        }

        // Cria o writer
        $this->addWriter(new \Zend\Log\Writer\Stream($filename));
    }


    /**
     * Grava uma mensagem no log
     *
     * @param  integer  $priority  Prioridade da mensagem
     * @param  string   $message   Messagem para gravar
     * @param  mixed    $extras    Dados adicionais que serão transformados em JSON
     * @return void
     */
    public function log($priority, $message , $extras = null)
    {
        parent::log($priority, $message, is_array($extras) ? $extras : array($extras));
    }

	/**
	 * Retorna a pasta raiz de onde são gravados o frete
	 *
     * @return string
     */
    public function getRootFolder()
    {
        return $this->_rootFolder;
    }

}
