<?php


class ilCertificateCron
{
	/**
	 * @var ilCertificateQueueRepository
	 */
	private $queueRepository;

	/**
	 * @var ilCertificateTemplateRepository
	 */
	private $templateRepository;

	/**
	 * @var ilUserCertificateRepository
	 */
	private $userRepository;

	/**
	 * @param ilCertificateQueueRepository $queueRepository
	 * @param ilCertificateTemplateRepository $templateRepository
	 * @param ilUserCertificateRepository $userRepository
	 */
	public function __construct(
		ilCertificateQueueRepository $queueRepository,
		ilCertificateTemplateRepository $templateRepository,
		ilUserCertificateRepository $userRepository
	) {
		$this->queueRepository = $queueRepository;
		$this->templateRepository = $templateRepository;
		$this->userRepository = $userRepository;
	}

	public function run()
	{
		$entries = $this->queueRepository->getAllEntriesFromQueue();

		foreach ($entries as $entry) {
			/** @var $entry ilCertificateQueueEntry */
			$class = $entry->getAdapterClass();
			$adapter = new $class();
			if (!$adapter instanceof ilCertificateCronAdapter) {
				throw new ilException('The given class ' . $class . ' MUST be an instance of ilCertificateCronAdapter.');
			}

			$objId = $entry->getObjId();
			$userId = $entry->getUserId();

			$template = $this->templateRepository->fetchCurrentlyActiveCertificate($objId);

			$object = ilObjectFactory::getInstanceByObjId($objId, false);
			$type = $object->getType();

			$userObject = ilObjectFactory::getInstanceByObjId($userId, false);
			if (!$userObject || !($userObject instanceof \ilObjUser)) {
				throw new ilException('The given user id"' . $userId . '" could not be referred to an actual user');
			}

			$certificateContent = $template->getCertificateContent();

			$placeholderValues = $adapter->getPlaceholderValues($userId, $objId);
			foreach ($placeholderValues as $placeholder => $value) {
				$certificateContent = str_replace('[' . $placeholder . ']', $value, $certificateContent );
			}

			$userCertificate = new ilUserCertificate(
				$template->getId(),
				$objId,
				$type,
				$userId,
				$userObject->getFullname(),
				$entry->getStartedTimestamp(),
				$certificateContent,
				json_encode($placeholderValues),
				null,
				$template->getVersion(),
				ILIAS_VERSION_NUMERIC,
				true
			);

			$this->userRepository->save($userCertificate);
		}
	}
}
