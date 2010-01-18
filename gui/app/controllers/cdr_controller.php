<?php
/****************************************************************************
 * cdr_controller.php	- Dispaly, delete, export of CDR 
 * version 		- 1.0.353
 * 
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 *
 * The Initial Developer of the Original Code is
 *   Louise Berthilson <louise@it46.se>
 *
 *
  ***************************************************************************/

class CdrController extends AppController{

      var $name = 'Cdr';
      var $helpers = array('Csv','Javascript');

      var  $paginate = array('limit' => 50, 'page' => 1, 'order' => array( 'Cdr.epoch' => 'desc')); 


      function refresh(){

      $this->autoRender = false;
 
      $this->Cdr->refresh();

      }


      function index(){


        if(isset($this->params['form']['submit'])) {
		if ($this->params['form']['submit']==__('Refresh',true)){
                   $this->requestAction('/cdr/refresh');
                   }
       }



      $this->pageTitle = 'Call Data Records';

      $this->Session->write('Cdr.source', 'index');

      if(isset($this->params['named']['sort'])) { 
      		$this->Session->write('cdr_sort',array($this->params['named']['sort']=>$this->params['named']['direction']));
		}
	elseif($this->Session->check('cdr_sort')) { 
  		$this->paginate['order'] = $this->Session->read('cdr_sort');
		} 

      if(isset($this->params['named']['limit'])) { 
	$this->Session->write('cdr_limit',$this->params['named']['limit']);
	}
	elseif($this->Session->check('cdr_limit')) { 
	$this->paginate['limit'] = $this->Session->read('cdr_limit');
	}	

	     $this->Cdr->recursive = 0; 

   	     $data = $this->paginate('Cdr');

	     $this->set('cdr',$data);  

	     }


    function delete ($id){

    	     $call_id = $this->Cdr->getCallId($id);

    	     if($this->Cdr->del($id))
	     {
	     $this->Session->setFlash('CDR with Call ID "'.$call_id.'" has been deleted.');
	     $this->log("Action: entry deleted; Call-ID: ".$call_id, "cdr"); 
	     }

             $this->redirect(array('action' => 'index'));


    }

    function process (){

    	    if(!empty($this->params['form']['cdr'])){
	   
	      $entries = $this->params['form']['cdr'];
    	      $action = $this->params['data']['Submit'];
    	      foreach ($entries as $key => $id){
    	     	    if ($id) {
		       $this->Cdr->id = $id;
		       if ($action == __('Delete',true)){
    	     	       	   $call_id = $this->Cdr->getCallId($id);
     	       	  	   if ($this->Cdr->del($id)){
	     		      $this->log("Action: entry deleted; Call-ID: ".$call_id, "cdr"); 
			    }
			}
		    }
	      }
	     }
	  $this->redirect(array('action' => 'index'));

    }

    function output(){

      if ($this->data['Cdr']){
      	 $start	  = $this->data['Cdr']['start_time'];
      	 $end 	  = $this->data['Cdr']['end_time'];
 

	$hour_start = $start['hour'];
	$hour_end   = $end['hour'];

	if($start['meridian']=='pm' && $start['hour']!=12){ 
	   	$hour_start=$hour_start+12;
		} 
 	elseif($start['meridian']=='am' && $start['hour']==12){ 
	   	$hour_start='00';
	}

	if($end['meridian']=='pm' && $end['hour']!=12){ 
	   	$hour_end=$hour_end+12;
		} 
 	elseif($end['meridian']=='am' && $end['hour']==12){ 
	   	$hour_end='00';
	}

	 $start = $start['year'].'-'.$start['month'].'-'.$start['day'].' '.$hour_start.':'.$start['min'].':00';
     	 $end   = $end['year'].'-'.$end['month'].'-'.$end['day'].' '.$hour_end.':'.$end['min'].':00';


      	 $start_epoch = strtotime($start);
      	 $end_epoch = strtotime($end);

	 $param = array('conditions' => array('epoch >=' => $start_epoch, 'epoch <=' => $end_epoch));
    	 $this->set('data', $this->Cdr->find('all',$param)); 

	 } else {

    	   $this->set('data', $this->Cdr->findAll()); 
	 }


  	     Configure::write('debug', 0);
 	 
    	     $this->layout = null;
    	     $this->autoLayout = false;

    	     $this->render();    
    }


      function export(){


    	     $this->render();  
       }


      function overview(){

       	$this->pageTitle = 'Call Data Records : Overview';

        if(isset($this->params['form']['submit'])) {
		if ($this->params['form']['submit']==__('Refresh',true)){
                   $this->requestAction('/cdr/refresh');
                   }
         } 

         if($this->data){
		$epoch = $this->Cdr->dateToEpoch($this->data['Cdr']);
       		$param = array('conditions' => array('epoch >=' => $epoch['start'], 'epoch <=' => $epoch['end']));
       		$this->set('cdr', $this->Cdr->find('all',$param)); 
          } else {
       	       $this->set('cdr',$this->Cdr->find('all'));  
                 }
      }


}
?>
