<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.SuperExtraVote
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php
// Create option list for voting select box
$options = array();
$uri = clone JUri::getInstance();
$uri->setVar('hitcount', '0');
?>

<div class="superextravote">
    <?php $class='';
    if(!$vote->access){
        $class .= ' locked';
        if(!$this->user->id){
            $class .= ' choosable';
        }
    }?>
    <form method="post"
          id="<?php echo 'superextravote_' . $this->article_id; ?>"
          action="/<?php /*echo htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8');*/ ?>"
          class="form-inline<?php echo $class;?>">
        <div class="stars">
        <?php for($s=5; $s >= 1; $s--){ ?>
            <input name="superextravote_<?php echo (int) $this->article_id; ?>"
                   value="<?php echo (int) $s; ?>"
                   type="radio"
                   tabindex="<?php echo (int) $s; ?>"
                   id="superextravote_<?php echo (int) $this->article_id . (int) $s; ?>"
                   onchange="javascript:SuperExtraVote(<?php echo (int) $this->article_id; ?>, <?php echo (int) $s; ?>)">
            <label for="superextravote_<?php echo (int) $this->article_id . (int) $s;; ?>" title="<?php echo (int) $s;?>">
                <?php if($vote->rating >= $s){
                    $star=' chose-full';
                }
                else if ($vote->rating == 0.5 || $vote->rating == $s - 0.5){
                    $star=' chose-half';
                }
                else{
                    $star='';
                }?>
                <span class="star<?php echo $star; ?>"></span>
            </label>
        <?php }?>
        </div>
        <input type="hidden" name="task" value="article.vote" />
        <input type="hidden" name="hitcount" value="0" />
        <input type="hidden" name="url" value="<?php echo htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8'); ?>" />
        <div class="rating">
            <span>средняя оценка: <b><?php echo $vote->rating;?></b></span>
        </div>
        <div class="count">
            <span>вего оценок: <b><?php echo $vote->rating_count;?></b></span>
        </div>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>