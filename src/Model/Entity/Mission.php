<?php
namespace assigntask\Model\Entity;

use Cake\ORM\Entity;

/**
 * Mission Entity
 *
 * @property int $id
 * @property int $from_id
 * @property int $to_id
 * @property string $mission
 * @property \Cake\I18n\Time $schedule
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \assigntask\Model\Entity\From $from
 * @property \assigntask\Model\Entity\To $to
 */
class Mission extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
