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
    if ($netType === '2-0') {
        return pow(2, $phaseNo + 1) === (int)$netSize;
    } else {
        switch ($phaseType) {
            case 'o':
                return pow(2, $phaseNo + 1) === ((int)$netSize / 4);

            case 'w':
                return $phaseNo === 2;

            case 'l':
                return $phaseNo === 3;
        }
    }
}

function getPlayersActions($currentPhasePlacement, $net_size, $net_type, $phaseType, $phaseNo) {
    return getJSON([
        'winner' => getWinnerAction($currentPhasePlacement, $net_size, $net_type, $phaseType, $phaseNo),
        'loser' => getLoserAction($currentPhasePlacement, $net_size, $net_type, $phaseType, $phaseNo)
    ]);
}

function getLoserAction($currentPhasePlacement, $net_size, $net_type, $phaseType, $phaseNo) {
    $action = [
        'place' => null,
        'targetGame' => null
    ];

    if ($net_type === '2-0') {
        $action['place'] = pow(2, log((int)$net_size, 2) - $phaseNo - 1) + 1;
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
                        $fromPhasePlacement = (int)$currentPhasePlacement;
                        if ($fromPhasePlacement % 2 === 0) {
                            $phasePlacement = $fromPhasePlacement / 2;
                            $targetPlayerSlot = 1;
                        } else {
                            $phasePlacement = ($fromPhasePlacement - 1) / 2;
                            $targetPlayerSlot = 2;
                        }

                        break;

                    case 1:
                        // Здесь каждый должен упасть крест-накрест со своим положением в верхней сетке
                        $phasePlacement = (int)$net_size / 4 - (int)$currentPhasePlacement - 1;

                        break;

                    case 2:
                        // В сетке на 16 меняем местами
                        if ((int)$net_size === 16) {
                            $phasePlacement = (int)$currentPhasePlacement === 1 ? 0 : 1;
                            break;
                        // В сетке на 32 перетасовываем пары
                        } elseif ((int)$net_size === 32) {
                            $wasPhasePlacement = (int)$currentPhasePlacement;
                            $phasePlacement = $wasPhasePlacement % 2 === 0
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
                        $fromPhasePlacement = (int)$currentPhasePlacement;
                        $fourNumber = floor($fromPhasePlacement / 4);
                        $gameNumberInFour = $fromPhasePlacement % 4;

                        $targetGameNumberInFour = $gameNumberInFour + 2 > 3
                            ? $gameNumberInFour - 2
                            : $gameNumberInFour + 2;

                        $phasePlacement = $fourNumber * 4 + $targetGameNumberInFour;

                        break;

                    default:
                        $currentPhasePlacement;
                }

                $action['targetGame']['phasePlacement'] = $phasePlacement;
                
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

                $action['place'] = (int)$net_size * $placeMupltiplierMap[$phaseNo] + 1;

                break;
            case 'o':
                $action['place'] = pow(2, log((int)$net_size / 4, 2) - $phaseNo - 1) + 1;

                break;
        }
    }

    return $action;
}

function getWinnerAction($currentPhasePlacement, $net_size, $net_type, $phaseType, $phaseNo) {
    $action = [
        'place' => null,
        'targetGame' => null
    ];
    $isLastPhase = getIsLastPhase($phaseType, $phaseNo, $net_type, $net_size);

    if ($net_type == '2-0') {
        if ($isLastPhase) {
            $action['place'] = 1;
        } else {
            $action['targetGame'] = [
                'phase' => $phaseType . 'w' . ((int)$phaseNo + 1),
                'phasePlacement' => (int)$phasePlacement / 2,
                'position' => (int)$phasePlacement % 2 === 0 ? 1 : 2
            ];
        }
    } else {
        if ($isLastPhase) {
            switch ($phaseType) {
                case 'o':
                    $action['place'] = 1;

                case 'w':
                    $action['targetGame'] = [
                        'phase' => 'o0',
                        'phasePlacement' => (int)$phasePlacement,
                        'position' => 1
                    ];

                case 'l':
                    $action['targetGame'] = [
                        'phase' => 'o0',
                        'phasePlacement' => (int)$phasePlacement,
                        'position' => 2
                    ];
            }
        } else {
            $newPhasePlacement = ($phaseType === 'l' && $phaseNo % 2 === 0)
                ? (int)$phasePlacement
                : floor((int)$phasePlacement / 2);

            if ($phaseType === 'l' && $phaseNo % 2 === 0) {
                $position = 2;
            } else {
                if ((int)$phasePlacement % 2 === 0) {
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