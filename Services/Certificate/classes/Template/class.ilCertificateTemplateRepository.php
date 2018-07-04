<?php


class ilCertificateTemplateRepository
{
	/**
	 * @var ilDBInterface
	 */
	private $database;

	/**
	 * @param ilDBInterface $database
	 */
	public function __construct(\ilDBInterface $database)
	{
		$this->database = $database;
	}

	/**
	 * @param ilCertificateTemplate $certificateTemplate
	 */
	public function save(ilCertificateTemplate $certificateTemplate)
	{
		$version = $this->fetchLatestVersion($certificateTemplate->getObjId());
		$version += 1;

		$id = $this->database->nextId('certificate_template');

		$this->database->insert('certificate_template', array(
			'id'                  => array('integer', $id),
			'obj_id'              => array('integer', $certificateTemplate->getObjId()),
			'certificate_content' => array('clob', $certificateTemplate->getCertificateContent()),
			'certificate_hash'    => array('clob', $certificateTemplate->getCertificateHash()),
			'template_values'     => array('clob', $certificateTemplate->getTemplateValues()),
			'version'             => array('clob', $version),
			'ilias_version'       => array('clob', $certificateTemplate->getIliasVersion()),
			'created_timestamp'   => array('integer', $certificateTemplate->getCreatedTimestamp()),
			'currently_active'    => array('integer', (integer) $certificateTemplate->isCurrentlyActive())
		));
	}

	public function fetchCertificateTemplatesByObjId($objId)
	{
		$result = array();

		$sql = 'SELECT * FROM certificate_template WHERE obj_id = ' . $this->database->quote($objId, 'integer');

		$query = $this->database->query($sql);

		while ($row = $this->database->fetchAssoc($query)) {
			$result[] = new ilCertificateTemplate(
				$row['obj_id'],
				$row['certificate_content'],
				$row['certificate_hash'],
				$row['template_values'],
				$row['version'],
				$row['ilias_version'],
				$row['created_timestamp'],
				(boolean) $row['currently_active'],
				$row['id']
			);
		}

		return $result;
	}

	public function fetchCurrentlyActiveCertificate($objId)
	{
		$sql = 'SELECT * FROM certificate_template
WHERE obj_id = ' . $this->database->quote($objId, 'integer') . '
AND currently_active = 1
';

		$query = $this->database->query($sql);

		while ($row = $this->database->fetchAssoc($query)) {
			return new ilCertificateTemplate(
				$row['obj_id'],
				$row['certificate_content'],
				$row['certificate_hash'],
				$row['template_values'],
				$row['version'],
				$row['ilias_version'],
				$row['created_timestamp'],
				(boolean) $row['currently_active'],
				$row['id']
			);
		}

		throw new ilException('Not certificate template found for obj_id:' . $objId);
	}

	private function fetchLatestVersion($objId)
	{
		$templates = $this->fetchCertificateTemplatesByObjId($objId);

		$version = 0;
		foreach ($templates as $template) {
			if ($template->getVersion() > $version) {
				$version = $template->getVersion();
			}
		}

		return $version;
	}
}
