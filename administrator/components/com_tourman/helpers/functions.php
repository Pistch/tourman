<?php
function safe_get($array, $paramName, $defaultValue) {
    if (isset($array[$paramName])) {
        return $array[$paramName];
    }

    return $defaultValue;
}

function getJSON($arr) {
    return json_encode($arr, JSON_UNESCAPED_UNICODE);
}

function getLoserAndWinner($match) {
    if ((int)$match['pl1_score'] > (int)$match['pl2_score']) {
        return [
            'winner' => $match['pl1_id'],
            'loser' => $match['pl2_id']
        ];
    } elseif ((int)$match['pl1_score'] < (int)$match['pl2_score'])  {
        return [
            'winner' => $match['pl2_id'],
            'loser' => $match['pl1_id']
        ];
    } else {
        return null;
    }
}

function getIsLastPhase($phaseType, $phaseNo, $netType, $netSize) {
    $phaseNo = (int)$phaseNo;
    $netSize = (int)$netSize;

    if ($netType === '2-0') {
        return pow(2, $phaseNo + 1) === $netSize;
    } else {
        switch ($phaseType) {
            case 'o':
                $olympic_denominator = $netSize > 8 ? 4 : 2;

                return pow(2, $phaseNo + 1) === ($netSize / $olympic_denominator);

            case 'w':
                return $netSize > 8 ? $phaseNo === 2 : $phaseNo === 1;

            case 'l':
                return $netSize > 8 ? $phaseNo === 3 : $phaseNo === 1;
        }
    }
}

function getPlayersActions($currentPhasePlacement, $netSize, $netType, $phaseType, $phaseNo) {
    $phaseNo = (int)$phaseNo;
    $netSize = (int)$netSize;
    $currentPhasePlacement = (int)$currentPhasePlacement;

    return getJSON([
        'winner' => getWinnerAction($currentPhasePlacement, $netSize, $netType, $phaseType, $phaseNo),
        'loser' => getLoserAction($currentPhasePlacement, $netSize, $netType, $phaseType, $phaseNo)
    ]);
}

function getLoserAction($currentPhasePlacement, $netSize, $netType, $phaseType, $phaseNo) {
    $phaseNo = (int)$phaseNo;
    $netSize = (int)$netSize;
    $currentPhasePlacement = (int)$currentPhasePlacement;
    $action = [
        'place' => null,
        'targetGame' => null
    ];

    if ($netType === '2-0') {
        $action['place'] = (int)pow(2, log($netSize, 2) - $phaseNo - 1) + 1;
    } else {
        switch ($phaseType) {
            case 'w':
                // Падаем только в 1, 2 и 4 раунды сетки проигравших, 3й играется среди игроков, упавших ранее
                $targetPhaseMap = [0, 1, 3];
                $action['targetGame'] = [
                    'phase' => null,
                    'phasePlacement' => null,
                    'position' => null
                ];

                $newPhase = 'l' . (string)($targetPhaseMap[$phaseNo]);
                $action['targetGame']['phase'] = $newPhase;

                // Определяем игру, в которую падает проигравший, для каждого раунда свой алгоритм
                switch ($phaseNo) {
                    case 0:
                        // Тут всё просто, каждый падает в свою дырку
                        $fromPhasePlacement = $currentPhasePlacement;
                        if ($fromPhasePlacement % 2 === 0) {
                            $newPhasePlacement = (int)floor($fromPhasePlacement / 2);
                            $targetPlayerSlot = 1;
                        } else {
                            $newPhasePlacement = ($fromPhasePlacement - 1) / 2;
                            $targetPlayerSlot = 2;
                        }

                        break;

                    case 1:
                        // Здесь каждый должен упасть крест-накрест со своим положением в верхней сетке
                        $newPhasePlacement = $netSize / 4 - $currentPhasePlacement - 1;

                        break;

                    case 2:
                        // В сетке на 16 меняем местами
                        if ($netSize === 16) {
                            $newPhasePlacement = $currentPhasePlacement === 1 ? 0 : 1;
                            break;
                        // В сетке на 32 перетасовываем пары
                        } elseif ($netSize === 32) {
                            $wasPhasePlacement = $currentPhasePlacement;
                            $newPhasePlacement = $wasPhasePlacement % 2 === 0
                                ? $wasPhasePlacement + 1
                                : $wasPhasePlacement - 1;
                            break;
                        }

                        // На более крупных сетках механизм определения довольно сложен
                        // Сначала нужно разбить на четвёрки
                        // Затем крест-накрест поменять местами пары в четвёрке
                        // Получиться должно так:
                        // 1 -> 3
                        // 2 -> 4
                        // 3 -> 1
                        // 4 -> 2
                        // 5 -> 7
                        // 6 -> 8
                        // 7 -> 5
                        // 8 -> 6
                        $fromPhasePlacement = $currentPhasePlacement;
                        $fourNumber = (int)floor($fromPhasePlacement / 4);
                        $gameNumberInFour = $fromPhasePlacement % 4;

                        $targetGameNumberInFour = $gameNumberInFour + 2 > 3
                            ? $gameNumberInFour - 2
                            : $gameNumberInFour + 2;

                        $newPhasePlacement = $fourNumber * 4 + $targetGameNumberInFour;

                        break;

                    default:
                        $currentPhasePlacement;
                }

                $action['targetGame']['phasePlacement'] = $newPhasePlacement;
                
                if ($phaseNo === 0) {
                    // Специфика первого раунда сетки проигравших, нужно понимать, в верхний или нижний слот в игре
                    // следует определить игрока
                    $action['targetGame']['position'] = $targetPlayerSlot;
                } else {
                    $action['targetGame']['position'] = 1;
                }

                break;
            case 'l':
                // Высчитанные значения доли игроков, прошедших дальше того, для которого в данный момент определяем
                // пул занятых мест, при сетке до двух поражений с выходом в олимпийку
                $placeMupltiplierMap = [ 3/4, 1/2, 3/8, 1/4 ];

                $action['place'] = (int)($netSize * $placeMupltiplierMap[$phaseNo] + 1);

                break;
            case 'o':
                $olympic_denominator = $netSize > 8 ? 4 : 2;
                $action['place'] = (int)pow(2, log($netSize / $olympic_denominator, 2) - $phaseNo - 1) + 1;

                break;
        }
    }

    return $action;
}

function getWinnerAction($currentPhasePlacement, $netSize, $netType, $phaseType, $phaseNo) {
    $phaseNo = (int)$phaseNo;
    $netSize = (int)$netSize;
    $currentPhasePlacement = (int)$currentPhasePlacement;
    $action = [
        'place' => null,
        'targetGame' => null
    ];
    $isLastPhase = getIsLastPhase($phaseType, $phaseNo, $netType, $netSize);

    if ($netType == '2-0') {
        if ($isLastPhase) {
            $action['place'] = 1;
        } else {
            $action['targetGame'] = [
                'phase' => $phaseType . (string)($phaseNo + 1),
                'phasePlacement' => (int)floor($currentPhasePlacement / 2),
                'position' => $currentPhasePlacement % 2 === 0 ? 1 : 2
            ];
        }
    } else {
        if ($isLastPhase) {
            switch ($phaseType) {
                case 'o':
                    $action['place'] = 1;
                    break;

                case 'w':
                    $action['targetGame'] = [
                        'phase' => 'o0',
                        'phasePlacement' => $currentPhasePlacement,
                        'position' => 1
                    ];
                    break;

                case 'l':
                    $action['targetGame'] = [
                        'phase' => 'o0',
                        'phasePlacement' => $currentPhasePlacement,
                        'position' => 2
                    ];
                    break;
            }
        } else {
            $newPhasePlacement = ($phaseType === 'l' && $phaseNo % 2 === 0)
                ? $currentPhasePlacement
                : (int)floor($currentPhasePlacement / 2);

            if ($phaseType === 'l' && $phaseNo % 2 === 0) {
                $position = 2;
            } else {
                if ($currentPhasePlacement % 2 === 0) {
                    $position = 1;
                } else {
                    $position = 2;
                }
            }

            $action['targetGame'] = [
                'phase' => $phaseType . (string)($phaseNo + 1),
                'phasePlacement' => $newPhasePlacement,
                'position' => $position
            ];
        }
    }

    return $action;
}
?>

