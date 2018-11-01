<?php
use CRM_CiviGeometry_ExtensionUtil as E;

class CRM_CiviGeometry_BAO_Geometry extends CRM_CiviGeometry_DAO_Geometry {

  /**
   * Create a new Geometry based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_CiviGeometry_DAO_Geometry|NULL
   */
  public static function create($params) {
    $className = 'CRM_CiviGeometry_DAO_Geometry';
    $entityName = 'Geometry';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    if (!empty($params['id'])) {
      $instance->id = $params['id'];
      $instance->find();
    }
    if (!empty($params['geometry'])) {
      $params['geometry'] = CRM_Utils_Type::escape("ST_GeomFromGeoJSON('" . $params['geometry'] . "')", 'String');
    }
    $instance->copyValues($params);
    $instance->save();
    $instance->geometry = CRM_Core_DAO::singleValueQuery("SELECT ST_asGeoJSON(geometry) FROM civigeometry_geometry WHERE id = %1", [1 => [$id, 'Positive']]);
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

}