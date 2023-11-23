<?php

namespace Uol\Adapters;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use Uol\Enumerators\UolCategoriesEnum;

class UolMapCoursesMapper
{
    public static function map(array $plans, int $categoryId = 1): Collection
    {
        $plansCollection = collect($plans);
        $categories      = ConstantHelper::getAllConstants(UolCategoriesEnum::class);
        $plansFiltered   = $plansCollection
            ->where('id_categoria', $categoryId)
            ->where('disponivel', true)
            ->values();
        return $plansFiltered->map(function ($plan) use ($categories) {
            return [
                'operator' => Operations::UOL,
                'operation' => key(array_filter($categories, function ($category) use ($plan) {
                    return $category == data_get($plan, 'id_categoria');
                })),
                'product' => data_get($plan, 'id'),
                'image' => data_get($plan, 'url_imagem'),
                'label' => data_get($plan, 'nome'),
                'duration' => data_get($plan, 'duracao'),
                'details' => self::removeSpecialCharacters(data_get($plan, 'objetivo')),
                'price' => number_format(str_replace(
                    ',',
                    '.',
                    data_get($plan, 'curso_formato.curso_online.sem_tutoria.valor', 0)
                ), 2),
                'courseModules' => self::getContent(data_get($plan, 'conteudos.conteudo'))
            ];
        });
    }

    private static function getContent($content)
    {
        $hasId = array_has($content, ['id']);
        if ($hasId) {
            $content = array($content);
        }
        $contentToCollection = collect($content);
        $modules             = $contentToCollection->where('pai_id', 0);

        return $modules->map(function ($module) use ($contentToCollection) {
            $id         = data_get($module, 'id');
            $subModules = $contentToCollection->where('pai_id', $id);
            if ($subModules->isEmpty()) {
                return ['label' => data_get($module, 'titulo')];
            } else {
                return [
                  'label' => data_get($module, 'titulo'),
                  'subModules' => $subModules->map(function ($subModule) {
                      return data_get($subModule, 'titulo');
                  })->values()
                ];
            }
        })->values();
    }

    private static function removeSpecialCharacters($details)
    {
        $specialCharacters = ['<br/>', '<br>', '\t', '\\\\n', '\\n'];
        return str_replace($specialCharacters, "", $details);
    }
}
