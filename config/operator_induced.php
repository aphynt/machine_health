<?php

return [
    'connection' => env('OPERATOR_INDUCED_CONNECTION', 'sqlsrv'),

    'cat_source' => env('OPERATOR_INDUCED_CAT_SOURCE', 'DB_MACHINE_HEALTH.dbo.VW_OPR_CAT_ERROR_CODE'),
    'cat_reference' => env('OPERATOR_INDUCED_CAT_REFERENCE', 'dbo.REF_WARNING_CODE_CAT'),

    'komatsu_source' => env('OPERATOR_INDUCED_KOMATSU_SOURCE', 'DB_MACHINE_HEALTH.dbo.VW_OPR_KOMATSU_ERROR_CODE'),
    'komatsu_reference' => env('OPERATOR_INDUCED_KOMATSU_REFERENCE', 'dbo.REF_WARNING_CODE'),

    'operator_connection' => env('OPERATOR_INDUCED_OPERATOR_CONNECTION', 'sqlsrv'),
    'operator_source' => env('OPERATOR_INDUCED_OPERATOR_SOURCE', 'FOCUS.dbo.PRS_PERSONAL'),

    'map_connection' => env('OPERATOR_INDUCED_MAP_CONNECTION', 'sqlsrv'),
    'map_source' => env('OPERATOR_INDUCED_MAP_SOURCE', 'FOCUS.dbo.MAP_OBJECT'),
    'map_object_limit' => 3000,
    'map_max_vertices' => 800,

    'induced_value' => env('OPERATOR_INDUCED_VALUE', '1'),
    'require_operator' => true,
    'page_size' => 100,
    'max_map_points' => 1200,
    'max_range_days' => 31,
];
