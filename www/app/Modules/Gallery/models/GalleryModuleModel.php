<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsersModule
 *
 * @author Martin
 */
class GalleryModuleModel extends Model
{
    public function __create()
    {
        db::addSubst('photos', 'site_photos');
        db::addSubst('albums', 'site_albums');
    }

    public function _createAlbum($values)
    {
        db::addSubst('albums', 'site_albums');
        return db::insert(':albums:', $values)->execute(db::IDENTIFIER);
    }

    public function install()
    {
        db::query('CREATE TABLE IF NOT EXISTS `site_albums` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(300) NOT NULL,
                  `description` varchar(5000) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
        db::query('CREATE TABLE IF NOT EXISTS`site_photos` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `name` VARCHAR( 300 ) NOT NULL ,
                    `description` VARCHAR( 5000 ) NOT NULL ,
                    `album_id` INT NOT NULL ,
                    `file` VARCHAR( 1000 ) NOT NULL
                    ) ENGINE = InnoDB');
       return true;
    }

    public function _getAlbums()
    {
        db::addSubst('albums', 'site_albums');
        return db::select('*')->from(':albums:')->fetchAll();
    }

    public function _getPhotos($album_id)
    {
        db::addSubst('photos', 'site_photos');
        return db::select('*')->from(':photos:')->where('album_id = %i', $album_id)->fetchAll();
    }

    public function _getPhotosByAlbumName($album_name)
    {
        db::addSubst('photos', 'site_photos');
        db::addSubst('albums', 'site_albums');
        return db::select('*')->from(':photos:')->join(':albums:')->on(':albums:.id = :photos:.album_id')->where(':albums:.name = %s', $album_name)->fetchAll();
    }

    public function _savePhotos($values)
    {
        db::addSubst('photos', 'site_photos');
        $pos = db::select('MAX(position) as max')->from(':photos:')->fetch('max');
        $pos = (int)$pos['max'] + 1;
        $values['position'] = $pos;
        return db::insert(':photos:', $values)->execute(db::IDENTIFIER);
    }

    public function _getCoverPhotos($album_id, $limit = 4, $random = true)
    {
        db::addSubst('photos', 'site_photos');
        return db::select('*')->from(':photos:')->where('album_id = %i', $album_id)->limit($limit)->fetchAll();
    }

    public function uninstall()
    {
        return db::query('drop table :photos:')->execute() && db::query('drop table :albums:')->execute();
    }

    public function _getAlbumName($album_id)
    {
        db::addSubst('albums', 'site_albums');
        return db::select('name')->from(':albums:')->where('id= %i', $album_id)->fetch('name');
    }
}
?>
