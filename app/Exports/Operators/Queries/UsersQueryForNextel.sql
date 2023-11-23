SELECT (CASE
            WHEN isnull(replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.NEXTEL.cod'), '"', '')) THEN '-'
            ELSE replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.NEXTEL.cod'), '"', '')
        END) AS `Código PDV Nextel`,
       (CASE
            WHEN isnull(replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.NEXTEL.ref'), '"', '')) THEN '-'
            ELSE replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.NEXTEL.ref'), '"', '')
        END) AS `Código FTE Nextel`,
       concat(`users`.`firstName`, ' ', `users`.`lastName`) AS `Nome do vendedor`,
       `users`.`cpf` AS `CPF`,
       upper(`networks`.`slug`) AS `REDE`,
       `pointsOfSale`.`slug` AS `Codigo Loja`,
       `pointsOfSale`.`state` AS `UF`
FROM (((`users`
        JOIN `pointsOfSale_users` on((`pointsOfSale_users`.`userId` = `users`.`id`)))
       JOIN `pointsOfSale` on((`pointsOfSale`.`id` = `pointsOfSale_users`.`pointsOfSaleId`)))
      JOIN `networks` on((`networks`.`id` = `pointsOfSale`.`networkId`)))
JOIN `role_permissions` on((`users`.`roleId` = `role_permissions`.`roleId`))
JOIN `permissions` on((`role_permissions`.`permissionsId` = `permissions`.`id`))
WHERE (isnull(`users`.`deletedAt`)
       AND json_extract(`pointsOfSale`.`providerIdentifiers`, '$.NEXTEL') <> '-'
       AND (`networks`.`slug`) IN ($networks)
       AND (`permissions`.`slug` = 'SALE.CREATE'));