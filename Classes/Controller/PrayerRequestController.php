<?php
namespace VMFDS\VmfdsPrayerRequests\Controller;

    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2016 Christoph Fischer <christoph.fischer@volksmission.de>, Volksmission Freudenstadt
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

/**
 * PrayerRequestController
 */
class PrayerRequestController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * prayerRequestRepository
     *
     * @var \VMFDS\VmfdsPrayerRequests\Domain\Repository\PrayerRequestRepository
     * @inject
     */
    protected $prayerRequestRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        //$prayerRequests = $this->prayerRequestRepository->findAll();
        $prayerRequests = $this->prayerRequestRepository->findByAudience($this->settings['audience']);
        $answeredPrayers = $this->prayerRequestRepository->findByAudience($this->settings['audience'], 2);
        $this->view->assign('prayerRequests', $prayerRequests);
        $this->view->assign('answeredPrayers', $answeredPrayers);
    }

    /**
     * action show
     *
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @return void
     */
    public function showAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest = null)
    {
        if (is_null($prayerRequest)) {
            $this->forward('list');
        } else {
            $this->view->assign('prayerRequest', $prayerRequest);

        }
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {

    }

    /**
     * action create
     *
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $newPrayerRequest
     * @return void
     */
    public function createAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $newPrayerRequest)
    {
        $this->prayerRequestRepository->add($newPrayerRequest);
        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

        $this->addFlashMessage('Wir haben dein Gebetsanliegen erhalten und dir eine Bestätigung per E-Mail geschickt. Bitte beachte, dass ein Administrator dein Anliegen noch freischalten muss, bevor es erscheint.',
            '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->sendTemplateEmail(
            [$this->settings['mail']['admin'] => $this->settings['mail']['admin']],
            [$this->settings['mail']['from'] => $this->settings['mail']['from']],
            'Neues Gebetsanliegen',
            'NotifyNew',
            ['prayerRequest' => $newPrayerRequest]
        );
        $this->forward('show');
    }

    /**
     * action edit
     *
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @ignorevalidation $prayerRequest
     * @return void
     */
    public function editAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest = null)
    {
        if (is_null($prayerRequest)) {
            $this->forward('list');
        }
        if (!$this->request->hasArgument('code')) {
            $this->addFlashMessage('Gebetsanliegen können nur mit dem Link aus der Bestätigungsnachricht bearbeitet werden.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            $this->forward('show');
        }
        $editMode = 0;
        if ($prayerRequest->isValidAuthorCode($this->request->getArgument('code'))) {
            $editMode = 1;
        } // original Author
        if ($prayerRequest->isValidAdminCode($this->request->getArgument('code'))) {
            $editMode = 2;
        } // Admin
        if (!$editMode) {
            $this->addFlashMessage('Leider war der Bearbeitungscode nicht korrekt. Gebetsanliegen können nur mit dem Link aus der Bestätigungsnachricht bearbeitet werden.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            $this->forward('show');
        }
        $this->view->assign('answered', ($editMode == 1) && ($this->request->getArgument('answered')));
        $this->view->assign('prayerRequest', $prayerRequest);
        $this->view->assign('editMode', $editMode);
    }

    /**
     * action update
     *
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @return void
     */
    public function updateAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest)
    {
        if ($this->request->getArgument('editMode') == 1) { // edited by author
            $prayerRequest->setStatus(0); // reset to unconfirmed
            $this->prayerRequestRepository->update($prayerRequest);
            $this->addFlashMessage('Deine Bearbeitung wurde gespeichert, muss aber noch erneut von einem Administrator freigegeben werden.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $this->sendTemplateEmail(
                [$this->settings['mail']['admin'] => $this->settings['mail']['admin']],
                [$this->settings['mail']['from'] => $this->settings['mail']['from']],
                'Ein Gebetsanliegen wurde bearbeitet',
                'NotifyEdit',
                ['prayerRequest' => $prayerRequest]
            );
            $this->forward('show');
        } else { // edited by admin
            $this->prayerRequestRepository->update($prayerRequest);
            if ($prayerRequest->getStatus()) {
                $this->publish($prayerRequest, 'Publish', ['Bitte bete mit', 'Bitte betet mit']);
            }
            $this->addFlashMessage('Die Änderungen wurden gespeichtert.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $this->forward('show');
        }
    }

    /**
     * Publish a prayer request
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @param string $template Name of the mail template
     * @param array $subjects Message subjects (singluar and plural)
     */
    private function publish(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest, $template, $subjects)
    {
        switch ($prayerRequest->getAudience()) {
            case 5:
            case 4:
            case 3: // Prayer mailing list
                $this->sendTemplateEmail(
                    [$this->settings['mail']['list']['_typoScriptNodeValue'] => $this->settings['mail']['list']['_typoScriptNodeValue']],
                    [$this->settings['mail']['from'] => $this->settings['mail']['from']],
                    $subjects[1],
                    $template,
                    ['prayerRequest' => $prayerRequest, 'address' => 'Liebe Beter', 'plural' => 1]
                );
                $this->addFlashMessage('Das Gebetsanliegen wurde an die Gebetsliste versandt.',
                    '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            case 2: // elders
                $this->sendTemplateEmail(
                    [$this->settings['mail']['elders'] => $this->settings['mail']['elders']],
                    [$this->settings['mail']['from'] => $this->settings['mail']['from']],
                    $subjects[1],
                    $template,
                    ['prayerRequest' => $prayerRequest, 'address' => 'Liebe Älteste', 'plural' => 1]
                );
                $this->addFlashMessage('Das Gebetsanliegen wurde an die Ältesten versandt.',
                    '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            case 1: // pastor
                $this->sendTemplateEmail(
                    [$this->settings['mail']['pastor'] => $this->settings['mail']['pastor']],
                    [$this->settings['mail']['from'] => $this->settings['mail']['from']],
                    $subjects[0],
                    $template,
                    ['prayerRequest' => $prayerRequest, 'address' => 'Lieber Pastor', 'plural' => 0]
                );
                $this->addFlashMessage('Das Gebetsanliegen wurde an den Pastor versandt.',
                    '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        }
    }

    /**
     * action delete
     *
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @return void
     */
    public function deleteAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest)
    {
        $this->addFlashMessage('Dein Gebetsanliegen wurde gelöscht.',
            '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->prayerRequestRepository->remove($prayerRequest);
        $this->forward('list');
    }


    /**
     * action answered
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest
     * @return void
     */
    public function answeredAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest)
    {
        $prayerRequest->setStatus(2);
        $this->prayerRequestRepository->update($prayerRequest);
        $this->publish($prayerRequest, 'Testimony', ['Gebetserhörung!', 'Gebetserhörung!']);
        $this->sendTemplateEmail(
            [$prayerRequest->getEmail() => $prayerRequest->getAuthor()],
            [$this->settings['mail']['from'] => $this->settings['mail']['from']],
            'Deine Gebetserhörung',
            'Answered',
            ['prayerRequest' => $prayerRequest]
        );
        $this->addFlashMessage('Dein Gebetsanliegen wurde als erhört markiert. Wir freuen uns mit dir!',
            '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->forward('list');
    }

    /**
     * action resendConfirmation
     * send confirmation email with code
     *
     * @return void
     */
    public function resendConfirmationAction(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest = null
    ) {
        if (is_null($prayerRequest)) {
            $this->forward('list');
        } else {
            $this->sendConfirmation($prayerRequest);
            $this->addFlashMessage('Wir haben die Bestätigung erneut an die von dir angegebene E-Mailadresse gesandt.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $this->forward('show');
        }
    }

    /**
     * action listSignup
     * @return void
     */
    public function listSignupAction()
    {
    }

    /**
     * action listSubscribe
     * @return void
     */
    public function listSubscribeAction()
    {
        if ($this->request->hasArgument('email')) {
            // subscribe to mailman list
            $message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
            $message->setTo([$this->settings['mail']['list']['request'] => $this->settings['mail']['list']['request']])
                ->setFrom([$this->settings['mail']['from'] => $this->settings['mail']['from']])
                ->setSubject('subscribe address='.$this->request->getArgument('email'));
            $message->setBody('', 'text/html');
            $message->send();

            $this->addFlashMessage('Wir haben deine Anmeldung für die Mailingliste erhalten.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $this->forward('list');
        } else {
            $this->addFlashMessage('Bitte gib eine E-Mailadresse ein.',
                '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            $this->forward('listSignup');

        }
    }

    /**
     * Send confirmation mail
     * @param \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest Prayer request object
     */
    private function sendConfirmation(\VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest $prayerRequest)
    {
        $this->sendTemplateEmail(
            [$prayerRequest->getEmail() => $prayerRequest->getAuthor()],
            [$this->settings['mail']['from'] => $this->settings['mail']['from']],
            'Dein Gebetsanliegen',
            'ConfirmRequest',
            ['prayerRequest' => $prayerRequest]
        );
    }

    /**
     * Send an E-mail created by rendering a stand-alone Fluid view
     * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     * @return boolean TRUE on success, otherwise false
     */
    protected function sendTemplateEmail(
        array $recipient,
        array $sender,
        $subject,
        $templateName,
        array $variables = array(),
        array $attachments = []
    ) {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths'][0]);
        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $extensionName = $this->request->getControllerExtensionName();
        $emailView->getRequest()->setControllerExtensionName($extensionName);
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();

        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        foreach ($attachments as $attachment) {
            $message->attach(\Swift_Attachment::fromPath($attachment));
        }

        // HTML Email
        $message->setBody($emailBody, 'text/html');

        $message->send();
        return $message->isSent();
    }


}