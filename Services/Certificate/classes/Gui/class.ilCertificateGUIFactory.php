<?php
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateGUIFactory
{
    /**
     * @var
     */
    private $dic;

    /**
     * @param \ILIAS\DI\Container|null $dic
     */
    public function __construct(\ILIAS\DI\Container $dic = null)
    {
        if (null === $dic) {
            global $DIC;
            $dic = $DIC;
        }
        $this->dic = $dic;
    }

    /**
     * @param ilObject $object
     * @return ilCertificateGUI
     * @throws ilException
     */
    public function create(\ilObject $object) : ilCertificateGUI
    {
        global $DIC;

        $type = $object->getType();
        $objectId = $object->getId();

        $logger = $DIC->logger()->cert();

        $templateRepository = new ilCertificateTemplateRepository($this->dic->database(), $logger);
        $deleteAction = new ilCertificateTemplateDeleteAction($templateRepository);

        switch ($type) {
            case 'tst':
                $placeholderDescriptionObject = new ilTestPlaceholderDescription();
                $placeholderValuesObject = new ilTestPlaceHolderValues();

                $certificatePath = ilCertificatePathConstants::TEST_PATH . $objectId . '/';

                $formFactory = new ilCertificateSettingsTestFormRepository(
                    $object,
                    $certificatePath,
                    false,
                    $DIC->language(),
                    $DIC->ctrl(),
                    $DIC->access(),
                    $DIC->toolbar(),
                    $placeholderDescriptionObject
                );

                $certificatePath = ilCertificatePathConstants::TEST_PATH . $objectId . '/';

                $deleteAction = new ilCertificateTestTemplateDeleteAction(
                    $deleteAction,
                    new ilCertificateObjectHelper()
                );

                break;
            case 'crs':
                $hasAdditionalElements = true;

                $placeholderDescriptionObject = new ilCoursePlaceholderDescription();
                $placeholderValuesObject = new ilCoursePlaceholderValues();

                $certificatePath = ilCertificatePathConstants::COURSE_PATH . $objectId . '/';

                $formFactory = new ilCertificateSettingsCourseFormRepository(
                    $object,
                    $certificatePath,
                    false,
                    $DIC->language(),
                    $DIC->ctrl(),
                    $DIC->access(),
                    $DIC->toolbar(),
                    $placeholderDescriptionObject
                );

                $certificatePath = ilCertificatePathConstants::COURSE_PATH . $objectId . '/';
                break;
            case 'exc':
                $placeholderDescriptionObject = new ilExercisePlaceholderDescription();
                $placeholderValuesObject = new ilExercisePlaceHolderValues();

                $certificatePath = ilCertificatePathConstants::EXERCISE_PATH . $objectId . '/';

                $formFactory = new ilCertificateSettingsExerciseRepository(
                    $object,
                    $certificatePath,
                    false,
                    $DIC->language(),
                    $DIC->ctrl(),
                    $DIC->access(),
                    $DIC->toolbar(),
                    $placeholderDescriptionObject
                );

                $certificatePath = ilCertificatePathConstants::EXERCISE_PATH . $objectId . '/';
                break;
            case 'sahs':
                $placeholderDescriptionObject = new ilScormPlaceholderDescription($object);
                $placeholderValuesObject = new ilScormPlaceholderValues();

                $certificatePath = ilCertificatePathConstants::SCORM_PATH . $objectId . '/';

                $formFactory = new ilCertificateSettingsScormFormRepository(
                    $object,
                    $certificatePath,
                    true,
                    $DIC->language(),
                    $DIC->ctrl(),
                    $DIC->access(),
                    $DIC->toolbar(),
                    $placeholderDescriptionObject
                );

                $certificatePath = ilCertificatePathConstants::SCORM_PATH . $objectId . '/';

                break;
            default:
                throw new ilException(sprintf('The type "%s" is currently not defined for certificates', $type));
                break;
        }

        $gui = new ilCertificateGUI(
            $placeholderDescriptionObject,
            $placeholderValuesObject,
            $objectId,
            $certificatePath,
            $formFactory,
            $deleteAction
        );

        return $gui;
    }
}
