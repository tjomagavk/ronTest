<?php
// Отвечаем ТОЛЬКО на ajax запросы

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    return;
}
// данный код можно расширить добавив другие действия и указать их в case
$action = filter_input(INPUT_POST, 'action');

// Если в массиве POST нет действия - выход
if (empty($action)) {
    return;
}

// А если есть - работаем
$res = '';
switch ($action) {
    case 'getResources':
        // Задаём параметры для сниппета в AJAX запросе
        $params = array();
        $params['tpl'] = filter_input(INPUT_POST, 'tpl');
        $params['parents'] = filter_input(INPUT_POST, 'parents', FILTER_SANITIZE_NUMBER_INT);
        $params['offset'] = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT);
        $params['limit'] = filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT) == null ? 5 : filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT);
        $params['includeContent'] = filter_input(INPUT_POST, 'include_content', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'include_content', FILTER_SANITIZE_NUMBER_INT);
        $params['includeTVs'] = filter_input(INPUT_POST, 'include_tvs', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'include_tvs', FILTER_SANITIZE_NUMBER_INT);
        $params['processTVs'] = filter_input(INPUT_POST, 'process_tvs', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'process_tvs', FILTER_SANITIZE_NUMBER_INT);
        $params['showHidden'] = filter_input(INPUT_POST, 'show_hidden', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'show_hidden', FILTER_SANITIZE_NUMBER_INT);
        $res = $modx->runSnippet('getResources', $params);
        break;
    case 'getPage':
        // Задаём параметры для сниппета в AJAX запросе
        $params = array();
        $params['elementClass'] = 'modSnippet';
        $params['element'] = filter_input(INPUT_POST, 'element');
        $params['pageLimit'] = filter_input(INPUT_POST, 'page_limit');
        $params['pageNavVar'] = filter_input(INPUT_POST, 'page_nav_var');
        $params['tpl'] = filter_input(INPUT_POST, 'tpl');
        $params['parents'] = filter_input(INPUT_POST, 'parents', FILTER_SANITIZE_NUMBER_INT);
        $params['offset'] = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT);
        $params['limit'] = filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT) == null ? 5 : filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT);
        $params['includeContent'] = filter_input(INPUT_POST, 'include_content', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'include_content', FILTER_SANITIZE_NUMBER_INT);
        $params['includeTVs'] = filter_input(INPUT_POST, 'include_tvs', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'include_tvs', FILTER_SANITIZE_NUMBER_INT);
        $params['processTVs'] = filter_input(INPUT_POST, 'process_tvs', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'process_tvs', FILTER_SANITIZE_NUMBER_INT);
        $params['showHidden'] = filter_input(INPUT_POST, 'show_hidden', FILTER_SANITIZE_NUMBER_INT) == null ? 0 : filter_input(INPUT_POST, 'show_hidden', FILTER_SANITIZE_NUMBER_INT);
        $res = $modx->runSnippet('getPage', $params);
        break;

}
// Если у нас есть, что отдать на запрос - отдаем и прерываем работу парсера MODX
if (!empty($res)) {
    die($res);
}