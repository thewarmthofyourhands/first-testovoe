<?php

declare(strict_types=1);

namespace App\Enums;

enum NodeTypeEnum: string
{
    case DIRECT_COMPONENTS = 'Прямые компоненты';
    case PRODUCTS_AND_COMPONENTS = 'Изделия и компоненты';
    case EQUIPMENT_OPTIONS = 'Варианты комплектации';
}
