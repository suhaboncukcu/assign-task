<?php
namespace assigntask\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Missions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Froms
 * @property \Cake\ORM\Association\BelongsTo $Tos
 *
 * @method \assigntask\Model\Entity\Mission get($primaryKey, $options = [])
 * @method \assigntask\Model\Entity\Mission newEntity($data = null, array $options = [])
 * @method \assigntask\Model\Entity\Mission[] newEntities(array $data, array $options = [])
 * @method \assigntask\Model\Entity\Mission|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \assigntask\Model\Entity\Mission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \assigntask\Model\Entity\Mission[] patchEntities($entities, array $data, array $options = [])
 * @method \assigntask\Model\Entity\Mission findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MissionsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('missions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ]
            ]
        ]);

        $fromsConfig = Configure::read('Missions.Froms');
        $this->belongsTo('Froms', [
            'foreignKey' => 'from_id',
            'joinType' => 'INNER',
            'className' => $fromsConfig['className'],
            'targetForeignKey' => $fromsConfig['targetForeignKey']
        ]);

        $tosConfig = Configure::read('Missions.Tos');
        $this->belongsTo('Tos', [
            'foreignKey' => 'to_id',
            'joinType' => 'INNER',
            'className' => $tosConfig['className'],
            'targetForeignKey' => $tosConfig['targetForeignKey']
        ]);

        $this->belongsTo('ReferencedBys', [
            'className' => 'Missions',
            'foreignKey' => 'reference_mission'
        ]);

        $this->hasMany('ReferenceTos', [
            'className' => 'Missions',
            'foreignKey' => 'reference_mission'
        ]);


    }


    /**
     * Complete method
     *
     * @param entity $mission: a mission.
     * @return bool
     */
    public function complete($mission)
    {   
        $mission->completed = TRUE;
        $mission->completed_date = time();
        if ($this->save($mission)) {
            $event = new Event('Mission.completed', $this, [
                'mission' => $mission
            ]);
            $this->eventManager()->dispatch($event);

            return true;
        }

        return false;
    }

    /**
     * Assigns an existing method to someone else.
     *
     * @param entity $mission: a mission.
     * @return bool
     * 
     */
    public function assignTo($mission)
    {
        $missionEntity = $this->newEntity();
        $referencedById = $mission->id;
        $missionArray = $mission->toArray();
        unset($missionArray['id']);
        unset($missionArray['created']);
        unset($missionArray['modified']);
        unset($missionArray['reassigned']);
        $missionArray['reference_mission'] = $referencedById;
        $this->patchEntity($missionEntity, $missionArray);

        if ($this->save($missionEntity)) {

            $baseMission = $this->get($referencedById);
            $baseMission->reassigned = TRUE;
            $this->save($baseMission);


            $event = new Event('Mission.assigned', $this, [
                'mission' => $mission
            ]);
            $this->eventManager()->dispatch($event);




            return true;
        }

        return false;
    }


    public function findUncompleted(Query $query)
    {
        return $query->where(['Missions.completed' => false]);
    }

    public function findCompleted(Query $query)
    {
        return $query->where(['Missions.completed' => true]);
    }

    public function findUncompletedPassed(Query $query)
    {
        $now  = time();
        return $query
                    ->where(['Missions.completed' => false])
                    ->where(['Missions.schedule <=' => $now]);
    }

    public function findWOReassigned(Query $query)
    {
        return $query
                ->contain(['ReferenceTos'])
                ->where(['Missions.reassigned' => false]);
    }


    public function findParents(Query $query)
    {
        return $query->contain([
                        'ReferencedBys'
                    ]);
    }

    public function findChildren(Query $query)
    {
        return $query->contain([
                        'ReferenceTos'
                    ]);
    }



    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('mission', 'create')
            ->notEmpty('mission');

        $validator
            ->dateTime('schedule')
            ->allowEmpty('schedule');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        return $rules;
    }
}
