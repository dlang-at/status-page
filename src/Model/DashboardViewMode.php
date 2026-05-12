<?php

declare(strict_types=1);


namespace DlangAT\StatusPage\Model;

enum DashboardViewMode: string
{
    case Grid = 'grid';
    case List = 'list';
}
