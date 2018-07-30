<?php


class ilPdfGenerator
{
	/**
	 * @var ilUserCertificateRepository
	 */
	private $certificateRepository;

	public function __construct(ilUserCertificateRepository $userCertificateRepository)
	{
		$this->certificateRepository = $userCertificateRepository;
	}

	public function generate($userCertificateId)
	{
		$certificate = $this->certificateRepository->fetchCertificate($userCertificateId);

		$certificateContent = $certificate->getCertificateContent();
		$pdf_base64 = ilRpcClientFactory::factory('RPCTransformationHandler')
			->ilFO2PDF($certificateContent);

		ilUtil::deliverData(
			$pdf_base64->scalar,
			'Certificate.pdf',
			"application/pdf"
		);
	}
}