<?php
namespace Xiaozhu\config;

use Illuminate\Config\Repository;
class Packagetest
{
    /**
     * @var SessionManager
     */
    
    /**
     * @var Repository
     */
    protected $config;
    /**
     * Packagetest constructor.
     * @param SessionManager $session
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        
        $this->config = $config;
    }
    /**
     * @param string $msg
     * @return string
     */
    public  function test_rtn($msg = ''){
        //return 1;
        return $this->config;
    }
}
