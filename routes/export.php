<?php

Route::prefix('export')->group(function () {
    Route::post('/timbr/pointsofsale', 'Management\ExportOperator@pointsOfSaleTim');
    Route::post('/timbr/users', 'Management\ExportOperator@usersTim');
    Route::post('/claro/users', 'Management\ExportOperator@usersClaro');
    Route::post('/claro/pointsofsale', 'Management\ExportOperator@pointsOfSaleClaro');
});

