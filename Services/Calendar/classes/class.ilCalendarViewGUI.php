<?php
/* Copyright (c) 1998-2017 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Jesús López Reyes <lopez@leifos.com>
 * @version $Id$
 *
 * @ingroup ServicesCalendar
 */
class ilCalendarViewGUI
{
	const CAL_PRESENTATION_DAY = 1;
	const CAL_PRESENTATION_WEEK = 2;
	const CAL_PRESENTATION_MONTH = 3;
	const CAL_PRESENTATION_AGENDA_LIST = 9;

	/**
	 * @var \ILIAS\UI\Factory
	 */
	protected $ui_factory;

	/**
	 * @var \ILIAS\UI\Renderer
	 */
	protected $ui_renderer;

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;

	/**
	 * @var integer
	 */
	protected $presentation_type;

	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;

	/**
	 * @var ilLogger
	 */
	protected $logger;

	/**
	 * @var \ILIAS\UI
	 */
	protected $ui;

	/**
	 * @var bool true if the displayed view contains appointments.
	 */
	protected $view_with_appointments;

	/**
	 * @var ilLanguage
	 */
	protected $lng;
	
	/**
	 * @var ilObjUser
	 */
	protected $user;
	
	/**
	 * @var string
	 */
	protected $seed;
	
	
	/**
	 * 
	 * @param ilDate $seed
	 * @param int $presentation_type
	 */
	public function __construct(ilDate $seed, $presentation_type)
	{
		$this->seed = $seed;
		$this->initialize($presentation_type);
	}
	

	/**
	 * View initialization
	 * @param integer $a_calendar_presentation_type
	 */
	function initialize($a_calendar_presentation_type)
	{
		global $DIC;
		$this->ui_factory = $DIC->ui()->factory();
		$this->ui_renderer = $DIC->ui()->renderer();
		$this->ui = $DIC->ui();
		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->user = $DIC->user();
		$this->tabs_gui = $DIC->tabs();
		$this->tpl = $DIC["tpl"];
		$this->toolbar = $DIC->toolbar();
		$this->presentation_type = $a_calendar_presentation_type;
		$this->logger = $GLOBALS['DIC']->logger()->cal();
		//by default "download files" button is not displayed.
		$this->view_with_appointments = false;
	}

	/**
	 * Get app for id
	 *
	 * @param
	 * @return
	 */
	function getCurrentApp()
	{
		// @todo: this needs optimization
		$events = $this->getEvents();
		foreach ($events as $item)
		{
			if ($item["event"]->getEntryId() == (int) $_GET["app_id"])
			{
				return $item;
			}
		}
		return null;
	}

	/**
	 * Get events
	 * @todo public or protected
	 * @param
	 * @return 
	 */
	public function getEvents()
	{
		switch($this->presentation_type)
		{
			case self::CAL_PRESENTATION_AGENDA_LIST:

				if($this->period_end_day == "")
				{
					$this->period = ilCalendarAgendaListGUI::getPeriod();
					$end_date = clone $this->seed;
					switch ($this->period)
					{
						case ilCalendarAgendaListGUI::PERIOD_DAY:
							$schedule = new ilCalendarSchedule($this->seed,ilCalendarSchedule::TYPE_DAY);
							$end_date->increment(IL_CAL_DAY, 1);
							break;

						case ilCalendarAgendaListGUI::PERIOD_WEEK:
							$schedule = new ilCalendarSchedule($this->seed,ilCalendarSchedule::TYPE_WEEK);
							$end_date->increment(IL_CAL_WEEK, 1);
							break;

						case ilCalendarAgendaListGUI::PERIOD_MONTH:
							$schedule = new ilCalendarSchedule($this->seed,ilCalendarSchedule::TYPE_MONTH);
							$end_date->increment(IL_CAL_MONTH, 1);
							break;

						case ilCalendarAgendaListGUI::PERIOD_HALF_YEAR:
							$schedule = new ilCalendarSchedule($this->seed,ilCalendarSchedule::TYPE_HALF_YEAR);
							$end_date->increment(IL_CAL_MONTH, 6);
							break;
						default:
							// default is week ?!
							$schedule = new ilCalendarSchedule($this->seed,ilCalendarSchedule::TYPE_WEEK);
							$end_date->increment(IL_CAL_WEEK, 1);
							break;
					}

					$this->period_end_day = $end_date->get(IL_CAL_DATE);
				}
				else
				{
					$schedule = new ilCalendarSchedule($this->seed, ilCalendarSchedule::TYPE_PD_UPCOMING);
				}
				break;
			case self::CAL_PRESENTATION_DAY:
				$schedule = new ilCalendarSchedule($this->seed, ilCalendarSchedule::TYPE_DAY);
				break;
			case self::CAL_PRESENTATION_WEEK:
				$schedule = new ilCalendarSchedule($this->seed, ilCalendarSchedule::TYPE_WEEK);
				break;
			case self::CAL_PRESENTATION_MONTH:
				$schedule = new ilCalendarSchedule($this->seed, ilCalendarSchedule::TYPE_MONTH);
				break;
		}

		$schedule->addSubitemCalendars(true);
		$schedule->calculate();
		$ev = $schedule->getScheduledEvents();
		return $ev;
	}


	/**
	 * Get start/end date for item
	 *
	 * @param array $item item
	 * @return array
	 */
	function getDatesForItem($item)
	{
		$start = $item["dstart"];
		$end = $item["dend"];
		if($item["fullday"])
		{
			$start = new ilDate($start, IL_CAL_UNIX);
			$end = new ilDate($end, IL_CAL_UNIX);
		}
		else
		{
			$start = new ilDateTime($start, IL_CAL_UNIX);
			$end = new ilDateTime($end, IL_CAL_UNIX);
		}
		return array("start" => $start, "end" => $end);
	}

	/**
	 * Get modal for appointment (see similar code in ilCalendarBlockGUI)
	 */
	function getModalForApp()
	{
		$f = $this->ui_factory;
		$r = $this->ui_renderer;
		$ctrl = $this->ctrl;
		
		// set return class 
		$this->ctrl->setReturn($this, '');

		// @todo: this needs optimization
		$events = $this->getEvents();

		//item => array containing ilcalendary object, dstart of the event , dend etc.
		foreach ($events as $item)
		{
			if ($item["event"]->getEntryId() == (int) $_GET["app_id"] && $item['dstart'] == (int) $_GET['dt'])
			{
				$dates = $this->getDatesForItem($item);
				// content of modal
				include_once("./Services/Calendar/classes/class.ilCalendarAppointmentPresentationGUI.php");
				$next_gui = ilCalendarAppointmentPresentationGUI::_getInstance($this->seed, $item);
				$content = $ctrl->getHTML($next_gui);

				//plugins can change the modal title.

				$modal_title = ilDatePresentation::formatPeriod($dates["start"], $dates["end"]);
				$modal_title = $this->getModalTitleByPlugins($modal_title);
				$modal = $f->modal()->roundtrip($modal_title,$f->legacy($content));

				echo $r->renderAsync($modal);
			}
		}
		exit();
	}

	/**
	 * @param $a_calendar_entry
	 * @param $a_dstart
	 * @param string $a_title_forced  //used in plugins to rename the shy button title.
	 * @return string  shy button html
	 */
	function getAppointmentShyButton($a_calendar_entry, $a_dstart, $a_title_forced = "")
	{
		$f = $this->ui_factory;
		$r = $this->ui_renderer;

		$this->ctrl->setParameter($this, "app_id", $a_calendar_entry->getEntryId());
		$this->ctrl->setParameter($this,'dt',$a_dstart);
		$this->ctrl->setParameter($this,'seed',$this->seed->get(IL_CAL_DATE));
		$url = $this->ctrl->getLinkTarget($this, "getModalForApp", "", true, false);
		$this->ctrl->setParameter($this, "app_id", $_GET["app_id"]);
		$this->ctrl->setParameter($this, "dt", $_GET["dt"]);
		$this->ctrl->setParameter($this,'seed',$_GET["seed"]);

		$modal = $f->modal()->roundtrip('', [])->withAsyncRenderUrl($url);

		//Day view presents the titles with the full length.(agenda:class.ilCalendarAgendaListGUI.php)
		if($this->presentation_type == self::CAL_PRESENTATION_DAY) {
			$title = ($a_title_forced == "")? $a_calendar_entry->getPresentationTitle(false) : $a_title_forced;
		} else {
			$title = ($a_title_forced == "")? $a_calendar_entry->getPresentationTitle() : $a_title_forced;
		}


		$comps = [$f->button()->shy($title, "")->withOnClick($modal->getShowSignal()), $modal];

		return $r->render($comps);
	}

	//get active plugins.
	public function getActivePlugins($a_slot_id)
	{
		global $ilPluginAdmin;

		$res = array();

		foreach($ilPluginAdmin->getActivePluginsForSlot(IL_COMP_SERVICE, "Calendar", $a_slot_id) as $plugin_name)
		{
			$res[] = $ilPluginAdmin->getPluginObject(IL_COMP_SERVICE,
				"Calendar", $a_slot_id, $plugin_name);
		}

		return $res;
	}

	public function getModalTitleByPlugins($a_current_title)
	{
		$modal_title = $a_current_title;
		//demo of plugin execution.
		//"capm" is the plugin slot id for Appointment presentations (modals)
		foreach($this->getActivePlugins("capm") as $plugin)
		{
			$modal_title = ($new_title = $plugin->editModalTitle($a_current_title))? $new_title : "";
		}
		return $modal_title;
	}

	/**
	 * @param $a_cal_entry
	 * @param $a_start_date
	 * @param $a_title
	 * @return string
	 */
	public function getContentByPlugins($a_cal_entry, $a_start_date, $a_content)
	{
		$content = $a_content;

		//"capg" is the plugin slot id for AppointmentCustomGrid
		foreach($this->getActivePlugins("capg") as $plugin)
		{
			$plugin->setAppointment($a_cal_entry, new ilDateTime($a_start_date));
			if($new_content = $plugin->replaceContent($a_content))
			{
				$content = $new_content;
			}
			else
			{
				$shy_title = ($new_title = $plugin->editShyButtonTitle())? $new_title : "";
				if($shy_title)
				{
					$content = $this->getAppointmentShyButton($a_cal_entry, $a_start_date, $shy_title);
				}

				if($glyph = $plugin->addGlyph())
				{
					$content = $glyph." ".$content;
				}

				if($more_content = $plugin->addExtraContent())
				{
					$content = $content." ".$more_content;
				}
			}
		}
		if($content == $a_content)
		{
			return false;
		}

		return $content;
	}

	/**
	 * Add download link to toolbar
	 *
	 * //TODO rename this method to something like addToolbarDonwloadFiles
	 * @param
	 * @return
	 */
	function addToolbarActions()
	{
		$settings = ilCalendarSettings::_getInstance();

		if($settings->isBatchFileDownloadsEnabled())
		{
			if($this->presentation_type == self::CAL_PRESENTATION_AGENDA_LIST) {
				$num_events = $this->countEventsInView();
			}
			if ($this->view_with_appointments || $num_events)
			{
				$toolbar = $this->toolbar;
				$f = $this->ui_factory;
				$lng = $this->lng;
				$ctrl = $this->ctrl;

				// file download
				$add_button = $f->button()->standard($lng->txt("cal_download_files"),
					$ctrl->getLinkTarget($this, "downloadFiles"));
				$toolbar->addSeparator();
				$toolbar->addComponent($add_button);
			}
		}
	}

	/**
	 * Download files related to the appointments showed in the current calendar view (day,week,month,list). Not modals
	 */
	function downloadFiles()
	{
		include_once './Services/Calendar/classes/BackgroundTasks/class.ilDownloadFilesBackgroundTask.php';
		$download_job = new ilDownloadFilesBackgroundTask($GLOBALS['DIC']->user()->getId());

		$download_job->setBucketTitle($this->getBucketTitle());
		$download_job->setEvents($this->getEvents());
		$download_job->run();
		
		$GLOBALS['DIC']->ctrl()->redirect($this);
	}

	/**
	 * get proper label to add in the background task popover
	 * @return string
	 */
	public function getBucketTitle()
	{
		//definition of bucket titles here: 21365
		$user_settings = ilCalendarUserSettings::_getInstanceByUserId($this->user->getId());
		$bucket_title = $this->lng->txt("cal_calendar_download");

		switch ($this->presentation_type)
		{
			case self::CAL_PRESENTATION_DAY:
				$bucket_title .= " ".$this->seed->get(IL_CAL_DATE);
				break;
			case self::CAL_PRESENTATION_WEEK:
				$weekday_list = ilCalendarUtil::_buildWeekDayList($this->seed,$user_settings->getWeekStart())->get();
				$start = current($weekday_list);
				$char = strtolower(mb_substr($this->lng->txt("week"),0,1));
				$bucket_title .= " ".$start->get(IL_CAL_DATE)." 1$char";
				break;
			case self::CAL_PRESENTATION_MONTH:
				$year_month = $this->seed->get(IL_CAL_FKT_DATE,'Y-m','UTC');
				$char = strtolower(mb_substr($this->lng->txt("month"),0,1));
				$bucket_title .= " ".$year_month."-01 1".$char;
				break;
			case self::CAL_PRESENTATION_AGENDA_LIST:
				$bucket_title .= " ".$this->seed->get(IL_CAL_DATE);
				$get_list_option = ilSession::get('cal_list_view');
				switch ($get_list_option)
				{
					case ilCalendarAgendaListGUI::PERIOD_DAY:
						break;
					case ilCalendarAgendaListGUI::PERIOD_MONTH:
						$char = strtolower(mb_substr($this->lng->txt("month"),0,1));
						$bucket_title .= " 1$char";
						break;
					case ilCalendarAgendaListGUI::PERIOD_HALF_YEAR:
						$char = strtolower(mb_substr($this->lng->txt("month"),0,1));
						$bucket_title .= " 6$char";
						break;
					case ilCalendarAgendaListGUI::PERIOD_WEEK:
					default:
						$char = strtolower(mb_substr($this->lng->txt("week"),0,1));
						$bucket_title .= " 1$char";
						break;
				}
		}

		return $bucket_title;
	}

	/**
	 * get the events between 2 dates based in seed + view options.
	 * @return int number of events in the calendar list view.
	 */
	function countEventsInView()
	{
		$start = $this->seed;
		$end = clone $start;
		$get_list_option = ilCalendarAgendaListGUI::getPeriod();
		switch ($get_list_option)
		{
			case ilCalendarAgendaListGUI::PERIOD_DAY:
				$end->increment(IL_CAL_DAY,1);
				break;
			case ilCalendarAgendaListGUI::PERIOD_MONTH:
				$end->increment(IL_CAL_MONTH,1);
				break;
			case ilCalendarAgendaListGUI::PERIOD_HALF_YEAR:
				$end->increment(IL_CAL_MONTH,6);
				break;
			case ilCalendarAgendaListGUI::PERIOD_WEEK:
			default:
				$end->increment(IL_CAL_DAY,7);
				break;
		}
		$events = $this->getEvents();
		$num_events = 0;
		foreach($events as $event)
		{
			$event_start = $event['event']->getStart()->get(IL_CAL_DATE);
			$event_end = $event['event']->getEnd()->get(IL_CAL_DATE);
			if($event_start >= $start->get(IL_CAL_DATE) &&  $event_end< $end->get(IL_CAL_DATE))
			{
				$num_events++;
			}
		}
		return $num_events;
	}
}