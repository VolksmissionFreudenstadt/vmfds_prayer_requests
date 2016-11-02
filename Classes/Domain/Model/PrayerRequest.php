<?php
namespace VMFDS\VmfdsPrayerRequests\Domain\Model;

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
 * PrayerRequest
 */
class PrayerRequest extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * author
     *
     * @var string
     */
    protected $author = '';
    
    /**
     * publicAuthor
     *
     * @var string
     */
    protected $publicAuthor = '';

    /**
     * email
     * @var string
     */
    protected $email = '';
    
    /**
     * request
     *
     * @var string
     */
    protected $request = '';

    /**
     * story
     *
     * @var string
     */
    protected $story = '';

    /**
     * status
     *
     * @var int
     */
    protected $status = 0;
    
    /**
     * audience
     *
     * @var int
     */
    protected $audience = 0;
    
    /**
     * Returns the author
     *
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Sets the author
     *
     * @param string $author
     * @return void
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
    /**
     * Returns the publicAuthor
     *
     * @return string $publicAuthor
     */
    public function getPublicAuthor()
    {
        return $this->publicAuthor;
    }
    
    /**
     * Sets the publicAuthor
     *
     * @param string $publicAuthor
     * @return void
     */
    public function setPublicAuthor($publicAuthor)
    {
        $this->publicAuthor = $publicAuthor;
    }
    
    /**
     * Returns the request
     *
     * @return string $request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * Sets the request
     *
     * @param string $request
     * @return void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * @param string $story
     */
    public function setStory($story)
    {
        $this->story = $story;
    }


    /**
     * Returns the status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Sets the status
     *
     * @param int $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    /**
     * Returns the audience
     *
     * @return int $audience
     */
    public function getAudience()
    {
        return $this->audience;
    }
    
    /**
     * Sets the audience
     *
     * @param int $audience
     * @return void
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * Internal helper function, returns hashed code for a text
     * @param $text Text to hash
     * @return mixed
     */
    private function getSaltedText($text) {
        if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
            $objSalt = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance(NULL);
            if (is_object($objSalt)) {
                $saltedText = $objSalt->getHashedPassword($text);
            }
        }
        return $saltedText;
    }

    /**
     * Check a hashed code for its validity
     * @return bool
     */
    private function isValidCode($code, $plainText) {
        $success = false;
        if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
            $objSalt = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance($saltedPassword);
            if (is_object($objSalt)) {
                $success = $objSalt->checkPassword($plainText, $code);
            }
        }
        return $success;
    }

    /**
     * Check author code
     * @param string $code Code to be checked
     */
    public function isValidAuthorCode($code) {
        return $this->isValidCode($code, 'AUTHOR_CODE_'.$this->getUid());
    }

    /**
     * Check author code
     * @param string $code Code to be checked
     */
    public function isValidAdminCode($code) {
        return $this->isValidCode($code, 'ADMIN_CODE_'.$this->getUid());
    }
    /**

     * @return string Author code
     */
    public function getAuthorCode() {
        return $this->getSaltedText('AUTHOR_CODE_'.$this->getUid());
    }

    /**
     * @return string Admin code
     */
    public function getAdminCode() {
        return $this->getSaltedText('ADMIN_CODE_'.$this->getUid());
    }


    /**
     * @param string $code Code
     */
    public function setAuthorCode($code) {
        // empty method: author code is generated, not set!
    }



}