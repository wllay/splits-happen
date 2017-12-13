<?php 

$str_score = 'X7/9-X-88/-6XXX81';
//$str_score = '5/5/5/5/5/5/5/5/5/5/5';
//$str_score = '9-9-9-9-9-9-9-9-9-9-';
//$str_score = 'XXXXXXXXXXXX';

/* Take a string of a valid score in US Bowling
 * and create an array of the frames
 */
echo "bowling score string:" . $str_score . "\n";
$throws = preg_split('//', $str_score, -1, PREG_SPLIT_NO_EMPTY);
$frame = 1;
$frames = array();
$firstball = true;
$strike = false;

foreach($throws as $throw=>$pins){
    
    if($frame == 10){
        
        if($pins == 'X'){
            if(is_array($frames[$frame])){
                $frames[$frame][] = $pins;
            }else{
                $frames[$frame] = array($pins);
            }
            $strike = true;
            $firstball = true;
        }elseif(is_int((int)$pins)){
            if($firstball){
                
                if($strike){
                    $frames[$frame][] = $pins;
                    $firstball = false;
                }else{
                    $firstball = false;
                    $frames[$frame][] = $pins;
                    
                }
                
                continue;
            }else{
                $frames[$frame][] = $pins;
                $firstball = true;
            }
        }elseif($pins == '/'){
            if($firstball == false){
                $frames[$frame][] = $pins;
                
            }else{
                $firstball = false;
            }
        }elseif($pins == '-'){
            if($firstball){
                $firstball = false;
                continue;
            }else{
                $frames[$frame] = array($throws[$throw-1], $pins);
                $firstball = true;
            }
        }
        
    }else{

        if($pins == 'X'){
            $frames[$frame] = array('X');
            $firstball = true;
        }elseif(is_int((int)$pins)){
            if($firstball){
                $firstball = false;
                continue;
            }else{
                $frames[$frame] = array($throws[$throw-1], $pins);
                $firstball = true;
            }
        }elseif($pins == '/'){
            $frames[$frame] = array($throws[$throw-1], $pins);
            $firstball = true;
        }elseif($pins == '-'){
            if($firstball){
                $firstball = false;
                continue;
            }else{
                $frames[$frame] = array($throws[$throw-1], $pins);
                $firstball = true;
            }
        }
        $frame = count($frames) + 1;
    }
}


/*
 * Take an array of valid frames for a US Bowing score
 * and calculate the correct score
 */
$score = 0;
foreach ($frames as $frame=>$balls){

    if($frame != 10){
        foreach($balls as $pincount){
            if($pincount == 'X'){
                $next_two_throws = get_next_two_throws($frames,$frame,false);
                $score = $score + 10 + $next_two_throws;
            }elseif($pincount == '/'){
                if($balls[0] == '-'){
                    $first_throw = 0;
                }else{
                    $first_throw = (int)$balls[0];
                }
                $next_throw = get_next_throw($frames,$frame,false);
                $score = $score + (10 - $first_throw) + $next_throw;
            }else{
                if($pincount == '-'){
                    $score = $score + 0;
                }else{
                    $score = $score + (int)$pincount;
                }
            }
        }
    }else{
        
        switch ($balls[0]) {
            case 'X':
                $next_two_throws = get_next_two_throws($frames,$frame,true);
                $score = $score + 10 + $next_two_throws;
                break;
            case '-':
                if($balls[1] == '/'){
                    $next_throw = get_next_throw($frames,$frame,true);
                    $score = $score + 10 + $next_throw;
                }elseif($balls[1] == '-'){
                    $score = $score + 0;
                }else{
                    $score = $score + (int)$balls[1];
                }
                break;
            default:
                if($balls[1] == '/'){
                    $next_throw = get_next_throw($frames,$frame,true);
                    $score = $score + 10 + $next_throw;
                }elseif($balls[1] == '-'){
                    $score = $score + (int)$balls[0];
                }else{
                    $score = $score + (int)$balls[0] + (int)$balls[1];
                }
        }

    }
    
}
echo "score: " . $score . "\n";

/*
 * Get the next two throws to calculate score for a strike
 */

function get_next_two_throws($frames,$frame,$frame10 = false){
    
    if($frame10){
        if($frames[$frame][1] == 'X'){
            $totalpins = 10;
            if($frames[$frame][2] == 'X'){
                $totalpins = 20;
            }
        }elseif ($frames[$frame][1] == '-'){
            if($frames[$frame][2] == '/'){
                $totalpins = 10;
            }elseif($frames[$frame][2] == '-'){
                $totalpins = 0;
            }else{
                $totalpins = (int)$frames[$frame][2];
            }
        }else{
            if($frames[$frame][2] == '/'){
                $totalpins = 10;
            }elseif($frames[$frame][2] == '-'){
                $totalpins = (int)$frames[$frame][1];
            }else{
                $totalpins = (int)$frames[$frame][1] + (int)$frames[$frame][2];
            }
        }
    }else{
        $totalpins = 0;
        $next_frame = $frames[$frame + 1];
        if($next_frame[0] == 'X'){
            $totalpins = 10;
            if($frame < 9){
                $next_frame = $frames[$frame + 2];
                if($next_frame[0] == 'X'){
                    $totalpins = 20;
                }elseif($next_frame[0] == '-'){
                    $totalpins = 10;
                }else{
                    $totalpins = $totalpins + (int)$next_frame[0];
                }
            }else{
                $next_throw = $frames[$frame + 1][1];
                if($next_throw == 'X'){
                    $totalpins = 20;
                }elseif($next_throw == '-'){
                    $totalpins = 10;
                }else{
                    $totalpins = $totalpins + (int)$next_throw;
                }
            }
            
        }elseif($next_frame[0] == '-'){
            if($next_frame[1] == '/'){
                $totalpins = $totalpins + 10;
            }elseif($next_frame[1] == '-'){
                $totalpins = 0;
            }else{
                $totalpins = (int)$next_frame[1];
            }
        }else{
            if($next_frame[1] == '/'){
                $totalpins = 10;
            }elseif($next_frame[1] == '-'){
                $totalpins = (int)$next_frame[0];
            }else{
                $totalpins = (int)$next_frame[0] + (int)$next_frame[1];
            }
        }
    }
    return $totalpins;
}


/*
 * Get the next throw to calculate score for a spare
 */
function get_next_throw($frames,$frame,$frame10 = false){
    if($frame10){
        if($frames[$frame][2] == 'X'){
            $totalpins = 10;
        }elseif ($frames[$frame][2] == '-'){
            $totalpins = 0;
        }else{
            if($frames[$frame][2] == '/'){
                $totalpins = 10;
            }elseif($frames[$frame][2] == '-'){
                $totalpins = (int)$frames[$frame][1];
            }else{
                $totalpins = (int)$frames[$frame][1] + (int)$frames[$frame][2];
            }
        }
    }else{
        $totalpins = 0;
        $next_frame = $frames[$frame + 1];
        if($next_frame[0] == 'X'){
            $totalpins = 10;
        }elseif($next_frame[0] == '-'){
            $totalpins = 0;
        }else{
            $totalpins = (int)$next_frame[0];
        }
    }
    return $totalpins;
}




?>