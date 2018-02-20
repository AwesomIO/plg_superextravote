<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.SuperExtraVote
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
var_dump($this->vote);
$class='';

echo '<div class="article-rating">';

for($i=1, $mean=$this->vote->get('rating_mean'); $i<=5; $i++, $mean--){
    if($mean >= 1){
        $class='full ';
    }
    elseif($mean> 0 && $mean < 1){
        $class='half ';
    }
    else{
        $class='clear ';
    }
    $class .= 'star';
    echo '<div class="'.$class.'" data-rating="'.$i.'" title="'.$i.'">'.$i.'</div>';
}
echo '</div>';

ob_start();
$style = ob_get_clean();
ob_start();?>
<style>
    .article-rating{
        display: block;
        position: relative;
        margin: 5px 10px;
    }
    .star{
        display: inline-block;
        margin: 0 5px 0 0;
        padding: 0;
        height: 25px;
        width: 25px;
        position: relative;
        background-color: yellow;
    }
</style>
<?php
$style .= ob_get_clean();


?>
