<?php
$session->flash();

echo "<h1>".__('Archived messages',true)."</h1>";

echo $html->div("",$paginator->counter(array('format' => __("Message:",true)." %start% ".__("-",true)." %end% ".__("of",true)." %count% ")));

	if ($messages){

echo $form->create('Message',array('type' => 'post','action'=> 'process'));
echo "<table width=100%>";

echo $html->tableHeaders(array(
	'',
	$paginator->sort(__("New",true), 'new'),
 	$paginator->sort(__("Title",true), 'title'),
 	$paginator->sort(__("Rate",true), 'rate'),
 	$paginator->sort(__("Category",true), 'Category.name'),
 	$paginator->sort(__("Created",true), 'created'),
 	$paginator->sort(__("Last modified",true), 'modified'),
 	$paginator->sort(__("Length",true), 'length'),
	__("Edit",true),
	__("Delete",true),
	__("Listen",true)));



echo $form->hidden('source',array('value'=>'archive'));


      foreach ($messages as $key => $message){

      $status='';

	$id = $form->input($message['Message']['id'],array('type'=>'checkbox','label'=>false));

	if($message['Message']['new']){
		$status = $html->image("icons/new.png",array("alt" => "New"));
	}

	$title    = $html->link($message['Message']['title'],"/messages/view/{$message['Message']['id']}");
	$rate     = $this->element('message_status',array('rate'=>$message['Message']['rate']));
	$category = $message['Category']['name'];
	$created  = $time->niceShort($message['Message']['created']);
	$modified = $this->element('message_status',array('modified'=>$message['Message']['modified']));
	$length   = $formatting->epochToWords($message['Message']['length']);

	$edit     = $html->link($html->image("icons/edit.png", array("alt" => "Edit")),"/messages/edit/{$message['Message']['id']}",null, null, false);
	$delete   = $html->link($html->image("icons/delete.png", array("alt" => "Delete")),"/messages/delete/{$message['Message']['id']}",null, __("Are you sure you want to delete this message?",true),false);

	$listen   = $this->element('musicplayer_button',array('url'=>$message['Message']['url'],'file'=>$message['Message']['file'],'title'=>$message['Message']['title']));

     $row[$key] = array(
     		$id,
     		array($status,array('align'=>'center')),
		$title,
		array($rate,array('align'=>'center')),
		array($category,array('align'=>'center')),
		$created,		
		$modified,
		array($length,array('align'=>'center')),
		array($edit,array('align'=>'center')),
		array($delete,array('align'=>'center')),
		array($listen,array('align'=>'center')));
		

	}

     echo $html->tableCells($row,array('class'=>'darker'));


     }
     echo "</table>";



echo "<table>";
echo $html->tableCells(array(
     $form->submit(__('Delete',true),  array('name' =>'data[Submit]', 'class' => 'button')),
     $form->submit( __('Activate',true), array('name' =>'data[Submit]', 'class' => 'button')), 
     $paginator->counter(array('format' => 'Page %page% of %pages%')),
     $paginator->numbers()));
echo "</table>";
echo $form->end();

echo "<span>".__("No of messages per page: ",true);
echo $html->link('5','archive/limit:5',null, null, false)." | ";
echo $html->link('10','archive/limit:10',null, null, false)." | ";
echo $html->link('25','archive/limit:25',null, null, false)." | " ;
echo $html->link(__('All',true),'archive',null, null, false);
echo "</span>";



?>
