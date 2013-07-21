<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_fitness/assets/css/fitness.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_fitness');
$saveOrder	= $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=programs'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
        

	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_STARTTIME', 'a.starttime', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_CLIENT_ID', 'a.client_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_TRAINER_ID', 'a.trainer_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_LOCATION', 'a.location', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_SESSION_TYPE', 'a.session_type', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_SESSION_FOCUS', 'a.session_focus', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_STATUS', 'a.status', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_FRONTEND_PUBLISHED', 'a.frontend_published', $listDirn, $listOrder); ?>
				</th>
		
                <?php if (isset($this->items[0]->published)) { ?>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'programs.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
			</tr>
		</thead>
		<tfoot>
			<?php 
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
			<tr>
				<td colspan="<?php echo $colspan ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_fitness');
			$canEdit	= $user->authorise('core.edit',			'com_fitness');
			$canCheckin	= $user->authorise('core.manage',		'com_fitness');
			$canChange	= $user->authorise('core.edit.state',	'com_fitness');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<td>
					<?php echo $item->starttime; ?>
				</td>
				<td>
					<?php
                                        echo $this->getGroupClients($item->id, $item->client_id);
                                        ?>
				</td>
				<td>
					<?php echo JFactory::getUser($item->trainer_id)->name; ?>
				</td>
				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'programs.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_fitness&task=program.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->location); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->location); ?>
				<?php endif; ?>
				</td>
				<td>
					<?php echo $item->title; ?>
				</td>
				<td>
					<?php echo $item->session_type; ?>
				</td>
				<td>
					<?php echo $item->session_focus; ?>
				</td>
				<td id="status_button_<?php echo $item->id ?>" class="center">
                                    
					<?php echo $this->state_html($item->id, $item->status); ?>
				</td>
				<td>
                                    <?php $frontend_published =  $item->frontend_published; ?>
                                    <a id="frontend_published_<?php echo $item->id; ?>"  style="cursor:pointer;"  class="jgrid" title="Unpublish Item" >
                                        <span onclick="setFrontendPublished('<?php echo $item->id; ?>', '<?php echo $frontend_published; ?>')" 
                                              class="state <?php echo  $frontend_published ? 'publish' : 'unpublish'?>"></span>
                                    </a>
				</td>


                <?php if (isset($this->items[0]->published)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'programs.',true);?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'programs.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'programs.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'programs.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'programs.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
                <?php } ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="event_status_wrapper">
  <img class="hideimage " src="<?php echo JUri::base() ?>components/com_fitness/assets/images/close.png" alt="close" title="close"  onclick="hide_status_wrapper()">
      <a data-status="1" class="event_status__button event_status_pending set_status" href="javascript:void(0)">pending</a>
      <a data-status="2" class="event_status__button event_status_attended set_status" href="javascript:void(0)">attended</a>
      <a data-status="3" class="event_status__button event_status_cancelled set_status" href="javascript:void(0)">cancelled</a>
      <a data-status="4" class="event_status__button event_status_latecancel set_status" href="javascript:void(0)">late cancel</a>
      <a data-status="5" class="event_status__button event_status_noshow set_status" href="javascript:void(0)">no show</a>
</div>
            



<script type="text/javascript">
    function getScript(url,success) {
        var script = document.createElement('script');
        script.src = url;
        var head = document.getElementsByTagName('head')[0],
        done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState
                || this.readyState == 'loaded'
                || this.readyState == 'complete')) {
                done = true;
                success();
                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            }
        };
        head.appendChild(script);
    }
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',function() {
        $(document).ready(function(){

             $(".set_status").bind('click', function(e) {
                var event_status = $(this).data('status');
                eventSetStatus(event_status, event_id);
            });
        });
    });
    
    


    function setFrontendPublished(event_id, status) {
        var url = '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1'
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'goals',
               format : 'text',
               task : 'setFrontendPublished',
               event_id : event_id,
               status : status
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                var event_id = response.data.event_id;
                var status = response.data.status;
                var href = $("#frontend_published_" + event_id);
                if(status == '1') {
                    href.html('<span id="frontend_published_"' + event_id + ' class="state publish" onclick="setFrontendPublished(' + event_id + ', 1)"></span>');
                    
                } else {
                    href.html('<span id="frontend_published_"' + event_id + ' class="state unpublish" onclick="setFrontendPublished(' + event_id + ', 0)"></span>');
                }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }
    
    
    
    function openSetBox(id, status) {
         event_id = id;
         $(".event_status_wrapper").show();
         $(".event_status__button").show();
         if(status == 1)  $(".event_status_wrapper .event_status_pending").hide();
         if(status == 2)  $(".event_status_wrapper .event_status_attended").hide();
         if(status == 3)  $(".event_status_wrapper .event_status_cancelled ").hide();
         if(status == 4)  $(".event_status_wrapper .event_status_latecancel").hide();
         if(status == 5)  $(".event_status_wrapper .event_status_noshow").hide();

    }
    
    function hide_status_wrapper() {
        $(".event_status_wrapper").hide();
    }
    
    
    function eventSetStatus(event_status, event_id){
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=set_event_status';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    event_id : event_id,
                    event_status : event_status
                },
                dataType : 'text',
                success : function(event_status) {
                    hide_status_wrapper();
                    $("#status_button_" + event_id).html(event_status_html(event_status, event_id));
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
        });

    }
    
    
    function event_status_html(event_status, event_id) {
         if(event_status == 1)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
         if(event_status == 2)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_attended event_status__button" href="javascript:void(0)">attended</a>';
         if(event_status == 3)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"   class="open_status event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>';
         if(event_status == 4)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"   class="open_status event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>';
         if(event_status == 5)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_noshow event_status__button" href="javascript:void(0)">no show</a>';

    }
    
 
</script>
