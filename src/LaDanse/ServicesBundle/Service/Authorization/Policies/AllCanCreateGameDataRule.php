<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class AllCanCreateGameDataRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return (
            $evaluationCt->getAction() == ActivityType::REALM_CREATE
            || $evaluationCt->getAction() == ActivityType::GUILD_CREATE
        );
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        return true;
    }
}