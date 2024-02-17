<?php

namespace portalium\menu\models;

use yii\base\Model;
use portalium\data\ActiveDataProvider;
use portalium\menu\models\MenuItem;

/**
 * MenuItemSearch represents the model behind the search form of `portalium\menu\models\MenuItem`.
 */
class MenuItemSearch extends MenuItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_item', 'id_menu'], 'integer'],
            [['label', 'slug', 'icon', 'date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MenuItem::find($params);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_item' => $this->id_item,
            'id_menu' => $this->id_menu,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'icon', $this->icon]);

        return $dataProvider;
    }
}
