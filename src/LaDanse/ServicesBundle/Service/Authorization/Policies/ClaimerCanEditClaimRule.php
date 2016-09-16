<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class ClaimerCanEditClaimRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return
            $evaluationCt->getAction() == ActivityType::CLAIM_EDIT
            ||
            $evaluationCt->getAction() == ActivityType::CLAIM_REMOVE;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        /** @var Claim $claim */
        $claim = $evaluationCtx->getResourceValue();

        $account = $evaluationCtx->getSubject()->getAccount();

        return $claim->getAccount()->getId() == $account->getId();
    }
}