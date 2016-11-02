<?php

namespace LaDanse\ServicesBundle\Notification;

/**
 * Class ListFunctions
 *
 * Utility function to deal with lists of email addresses
 *
 * @package LaDanse\ServicesBundle\Notification
 */
class ListFunctions
{
    /**
     * @param array $list
     *
     * @return array
     */
    static public function sortList(array $list)
    {
        $listCopy = $list;

        sort($listCopy, SORT_STRING | SORT_FLAG_CASE);

        return $listCopy;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    static public function removeDuplicates(array $list)
    {
        $deduplicated = [];

        if (count($list) == 0)
        {
            return $deduplicated;
        }

        $lastEntry = $list[0];

        $deduplicated[] = $lastEntry;

        for($i = 1; $i < count($list); $i++)
        {
            if ($list[$i] != $lastEntry)
            {
                $deduplicated[] = $list[$i];

                $lastEntry = $list[$i];
            }
        }

        return $deduplicated;
    }

    /**
     * @param array $listOne
     * @param array $listTwo
     *
     * @return array
     */
    static public function getIntersection(array $listOne, array $listTwo)
    {
        $intersection = [];

        $oneCount = 0;
        $twoCount = 0;

        while (($oneCount < count($listOne)) && ($twoCount < count($listTwo)))
        {
            $oneItem = $listOne[$oneCount];
            $twoItem = $listTwo[$twoCount];

            $itemCmp = strcmp($oneItem, $twoItem);

            if ($itemCmp == 0)
            {
                $intersection[] = $oneItem;

                $oneCount++;
                $twoCount++;
            }
            else if ($itemCmp < 0)
            {
                // list two is ahead of list one, advance only list one to catch up

                $oneCount++;
            }
            else
            {
                // list one is ahead of list two, advance only list two to catch up

                $twoCount++;
            }
        }

        return $intersection;
    }
}