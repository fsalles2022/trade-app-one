SELECT upper(`networks`.`slug`)                             AS `REDE`,
       (CASE
          WHEN isnull(replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.OI'), '\"', '')) THEN '-'
          ELSE replace(json_extract(`pointsOfSale`.`providerIdentifiers`, '$.OI'), '\"', '')
         END)                                               AS `SAP_PDV_OI`,
       concat(`users`.`firstName`, ' ', `users`.`lastName`) AS `NOME PROMOTOR`,
       `users`.`cpf`                                        AS `CPF`,
       ''                                                   AS `DDD`,
       ''                                                   AS `TELEFONE`,
       '15/05/2018'                                         AS `DT ADMISSÃO`,
       ''                                                   AS `DT DEMISSÃO`,
       'ATIVO'                                              AS `STATUS`,
       'GRANDE VAREJO'                                      AS `CANAL`,
       'NAC'                                                AS `REGIONAL`,
       'FIXO'                                               AS `TIPO`,
       'VENDEDOR'                                           AS `RANGE`
FROM (((`users`
  JOIN `pointsOfSale_users` on ((`pointsOfSale_users`.`userId` = `users`.`id`)))
  JOIN `pointsOfSale` on ((`pointsOfSale`.`id` = `pointsOfSale_users`.`pointsOfSaleId`)))
  JOIN `networks` on ((`networks`.`id` = `pointsOfSale`.`networkId`)))
       JOIN `role_permissions` on ((`users`.`roleId` = `role_permissions`.`roleId`))
       JOIN `permissions` on ((`role_permissions`.`permissionsId` = `permissions`.`id`))
WHERE (isnull(`users`.`deletedAt`)
  AND json_extract(`pointsOfSale`.`providerIdentifiers`, '$.OI') <> '-'
  AND (`networks`.`slug`) IN ($networks)
  AND (`permissions`.`slug` = 'SALE.CREATE'));
