<?php

class Admin_Form_EventInfo extends Zend_Form
{
    /**
     * Prepare a sub form for display
     *
     * @param  string|Zend_Form_SubForm $spec
     * @return Zend_Form_SubForm
     */
    public function prepareSubForm($spec)
    {
        if (is_string($spec)) {
            $subForm = $this->{$spec};
        } elseif ($spec instanceof Zend_Form_SubForm) {
            $subForm = $spec;
        } else {
            throw new Exception('Invalid argument passed to ' .
                                __FUNCTION__ . '()');
        }
        $this->setSubFormDecorators($subForm)
             ->addSubmitButton($subForm)
             ->addSubFormActions($subForm);
        return $subForm;
    }
 
    /**
     * Add form decorators to an individual sub form
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function setSubFormDecorators(Zend_Form_SubForm $subForm)
    {
        $subForm->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl',
                                   'class' => 'zend_form')),
            'Form',
        ));
        return $this;
    }
 
    /**
     * Add a submit button to an individual sub form
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function addSubmitButton(Zend_Form_SubForm $subForm)
    {
        $subForm->addElement(new Zend_Form_Element_Submit(
            'save',
            array(
                'label'    => 'Save and continue',
                'required' => false,
                'ignore'   => true,
            )
        ));
        return $this;
    }
 
    /**
     * Add action and method to sub form
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function addSubFormActions(Zend_Form_SubForm $subForm)
    {
        $subForm->setAction('/admin/index/process')
                ->setMethod('post');
        return $this;
    }
    
    public function setEventId($eventId)
    {
        $hidden = $this->createElement('hidden', 'event_id');
        $hidden->setValue($eventId);
        $this->addElement($hidden);
    }

    public function create($numOfTicketTypes)
    {
        /*
         *  STEP 1
         */
        $step1 = new Zend_Form_SubForm();
        
        $email = $this->createElement('text', 'name', array(
            'label'      => 'Name: ',
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));     
        $email->addErrorMessage('Name please');
        $step1->addElement($email);

        $location = $this->createElement('text', 'location', array(
            'label'      => 'Location: ',
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));
        $location->addErrorMessage('Location please');
        $step1->addElement($location);
        
        $startTime = $this->createElement('text', 'start_time', array(
            'label'      => 'Event starts: ',
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
        ));
        $startTime->class = 'date-pick';
        $startTime->addErrorMessage('Start time please');
        $step1->addElement($startTime);
        
        $endTime = $this->createElement('text', 'end_time', array(
            'label'      => 'Event ends: ',
            'required'   => true,
            'filters'    => array('StringTrim','StripTags')
        ));
        $endTime->class = 'date-pick';
        $endTime->addErrorMessage('End time please');
        $step1->addElement($endTime);
        
        $details = $this->createElement('textarea', 'details', array(
            'label'      => 'Details: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $step1->addElement($details);        

        /*
         *  STEP 2
         */
        $step2 = new Zend_Form_SubForm();
        $i = 0;
        while($i < $numOfTicketTypes)
        {
            $ticketType = new Admin_Form_SubForm_TicketType();
            $ticketType->setDefault('order',$i);
            $step2->addSubForm($ticketType, $i);
            $i++;
        }
       
        
        $step2->addElement('submit', 'submit', array('label' => 'New Ticket Type'));
        /*
         *  STEP 3
         */
        $step3 = new Zend_Form_SubForm();

        $public = new Zend_Form_Element_Select("public");

        $public ->setLabel("Publicize, or keep it private")
        ->addMultiOptions(array(
            "1" => "Public",
            "0" => "Private"
        ));
        $step3->addElement($public);
        
        
        
        
        
        /*
         *  Overall actions
         */
        
        // Set legends
        $step1->setLegend('Step 1 - Add your Event Details');
        $step2->setLegend('Step 2 - Create Tickets');
        $step3->setLegend('Step 3 - Promote your Event Page');

        // Attach sub forms to main form
        $this->addSubForms(array(
            'step1'  => $step1,
            'step2'  => $step2,
            'step3'  => $step3
        ));
        
        $this->addElement('submit', 'submit', array('label' => 'Save event'));
    }
}