<?php 
namespace Dsc;

class System extends Object
{
    public $input = null;
    
    public function __construct($config=array())
    {
        parent::__construct( $config );
        
        $this->input = new \Joomla\Input\Input;
    }
        
    /**
     * 
     * @param unknown_type $message
     * @param unknown_type $type
     */
    public function addMessage($message, $type='message') 
    {
        $messages = \Base::instance()->get('SESSION.messages') ? \Base::instance()->get('SESSION.messages') : array();
        
        switch (strtolower($type)) {
            case "error":
                $type = "error";
                break;
            case "warn":
            case "warning":
            case "notice":
                $type = "notice";
                break;
            default:
                $type = "message";
                break;            
        }
        
        $messages = array_merge( $messages, array( array('message'=>$message, 'type'=>$type) ) );
        \Base::instance()->set('SESSION.messages', $messages);
    }
    
    public function getMessages($empty=true) 
    {
        $messages = \Base::instance()->get('SESSION.messages') ? \Base::instance()->get('SESSION.messages') : array();
        if ($empty) {
            \Base::instance()->set('SESSION.messages', array());
        }
        return $messages;
    }
    
    public function renderMessages() 
    {
        // Initialise variables.
        $buffer = null;
        $lists = null;
        
        // Get the message queue
        $messages = $this->getMessages();
        
        // Build the sorted message list
        if (is_array($messages) && !empty($messages))
        {
            foreach ($messages as $msg)
            {
                if (isset($msg['type']) && isset($msg['message']))
                {
                    $lists[$msg['type']][] = $msg['message'];
                }
            }
        }
        
        // Build the return string
        $buffer .= "\n<div id=\"system-message-container\">";
        
        // If messages exist render them
        if (is_array($lists))
        {
            $buffer .= "\n<dl id=\"system-message\">";
            foreach ($lists as $type => $msgs)
            {
                if (count($msgs))
                {
                    $buffer .= "\n<dt class=\"" . strtolower($type) . "\">" . $type . "</dt>";
                    $buffer .= "\n<dd class=\"" . strtolower($type) . " message\">";
                    $buffer .= "\n\t<ul>";
                    foreach ($msgs as $msg)
                    {
                        $buffer .= "\n\t\t<li>" . $msg . "</li>";
                    }
                    $buffer .= "\n\t</ul>";
                    $buffer .= "\n</dd>";
                }
            }
            $buffer .= "\n</dl>";
        }
        
        $buffer .= "\n</div>";
        
        return $buffer;        
    }
    
    /**
     * 
     * @return \Joomla\Registry\Registry
     */
    public function getSessionRegistry()
    {
        $registry = \Base::instance()->get('SESSION.system.registry');
        if (empty($registry) || !$registry instanceof \Joomla\Registry\Registry) {
            $registry = new \Joomla\Registry\Registry;
            \Base::instance()->set('SESSION.system.registry', $registry);
        }
        
        return $registry;
    }
    
    /**
     * Gets a user state.
     *
     * @param   string  $key      The path of the state.
     * @param   mixed   $default  Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     */
    public function getUserState($key, $default = null)
    {
        $registry = $this->getSessionRegistry();
    
        if (!is_null($registry))
        {
            return $registry->get($key, $default);
        }
    
        return $default;
    }
    
    /**
     * Gets the value of a user state variable.
     *
     * @param   string  $key      The key of the user state variable.
     * @param   string  $request  The name of the variable passed in a request.
     * @param   string  $default  The default value for the variable if not found. Optional.
     * @param   string  $type     Filter for the variable, for valid values see {@link \Joomla\Filter\InputFilter::clean()}. Optional.
     *
     * @return  object  The request user state.
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = $this->input->get($request, null, $type);
    
        // Save the new value only if it was set in this request.
        if ($new_state !== null)
        {
            $this->setUserState($key, $new_state);
        }
        else
        {
            $new_state = $cur_state;
        }
    
        return $new_state;
    }
    
    /**
     * Sets the value of a user state variable.
     *
     * @param   string  $key    The path of the state.
     * @param   string  $value  The value of the variable.
     *
     * @return  mixed  The previous state, if one existed.
     */
    public function setUserState($key, $value)
    {
        $registry = $this->getSessionRegistry();
    
        if (!is_null($registry))
        {
            return $registry->set($key, $value);
        }
    
        return null;
    }
    
    
    public function getDispatcher()
    {
        if (empty($this->dispatcher)) {
            $this->dispatcher = new \Joomla\Event\Dispatcher;
        }
        
        return $this->dispatcher;
        
        
    }
}
?>