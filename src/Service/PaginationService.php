<?php

namespace App\Service;

class PaginationService
{

    /**
     * Avoid page 0 and extra pages that doesn't exist
     *
     * @param integer $page Current page 
     * @param integer $total Number total of pages
     * @param integer $limit Limit of item by pages
     * @return integer Return min and max
     */
    public function setPageMinAndMax(int $page, int $total, int $limit): int
    {
        if($page <= 0) return 1;
        if($page >= $total / $limit ) return ceil($total / $limit);
        else return $page;
    }
}
