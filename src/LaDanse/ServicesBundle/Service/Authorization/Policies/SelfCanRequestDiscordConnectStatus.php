<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\Discord\DiscordAccessToken;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class SelfCanRequestDiscordConnectStatus extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return $evaluationCt->getAction() == ActivityType::AUTHZ_DISCORD_CONNECT_STATUS;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        /** @var DiscordAccessToken $accessToken */
        $accessToken = $evaluationCtx->getResourceValue();

        return $evaluationCtx->getSubject()->getAccount()->getId() == $accessToken->getAccount()->getId();
    }
}