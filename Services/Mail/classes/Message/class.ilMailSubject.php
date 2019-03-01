<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilMailSubject
{
	const MAIL_SUBJECT_PREFIX = '[ILIAS]';

	private $settings;

	/**
	 * @param ilSettings $settings
	 */
	public function __construct(ilSettings $settings)
	{
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function getSubjectPrefix()
	{
		$subjectPrefix = $this->settings->get('mail_subject_prefix');
		if (false === $subjectPrefix) {
			$subjectPrefix = self::MAIL_SUBJECT_PREFIX;
		}

		return $subjectPrefix;
	}
}
