<?php
namespace VMFDS\VmfdsPrayerRequests\Domain\Repository;

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
 * The repository for PrayerRequests
 */
class PrayerRequestRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Find all requests with a specific minimum audience and status
     * @param string $audience Audience
     * @param string $status Status (default 1 = enabled)
     * @return \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest
     */
    public function findByAudience($audience, $status = 1)
    {
        $q = $this->createQuery();
        $constraints = [
            $q->equals('status', $status),
            $q->greaterThanOrEqual('audience', $audience),
        ];
        return $q->matching($q->logicalAnd($constraints))->execute();
    }
}