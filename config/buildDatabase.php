<?php

print '<h1>Starting C6 setup and/or rebuild</h1>';


$db = \CarbonPHP\Database::database();

try {
    $db->prepare('SELECT 1 FROM `carbon_users` LIMIT 1;')->execute();
    print '<br>Table `carbon_users` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE carbon_users
( 
	user_id VARCHAR(225) NOT NULL 
	PRIMARY KEY,
	user_type VARCHAR(20) NOT NULL,
	user_sport VARCHAR(20) NULL,
	user_session_id VARCHAR(225) NULL,
	user_facebook_id VARCHAR(225) NULL,
	user_username VARCHAR(25) NOT NULL,
	user_first_name VARCHAR(25) NOT NULL,
	user_last_name VARCHAR(25) NOT NULL,
	user_profile_pic VARCHAR(225) NULL,
	user_profile_uri VARCHAR(225) NULL,
	user_cover_photo VARCHAR(225) NULL,
	user_birthday TEXT NULL,
	user_gender VARCHAR(25) NULL,
	user_about_me TEXT NULL,
	user_rank INT(8) DEFAULT '0' NULL,
	user_password VARCHAR(225) NULL,
	user_email VARCHAR(50) NULL,
	user_email_code VARCHAR(225) NULL,
	user_email_confirmed VARCHAR(20) DEFAULT '0' NOT NULL,
	user_generated_string VARCHAR(200) NULL,
	user_membership INT(10) DEFAULT '0' NULL,
	user_deactivated TINYINT(1) DEFAULT '0' NULL,
	user_last_login VARCHAR(14) NOT NULL,
	user_ip VARCHAR(20) NOT NULL,
	user_education_history TEXT NULL,
	user_location TEXT NULL,
	user_creation_date VARCHAR(14) NULL,
	CONSTRAINT user_user_profile_uri_uindex
		UNIQUE (user_profile_uri),
	CONSTRAINT user_entity_entity_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1
;  
END;
    $db->exec($sql);
    print '<br>Table `user` Created';
}



try {
    $db->prepare('SELECT 1 FROM carbon_locations LIMIT 1;')->execute();
    print '<br>Table `carbon_location` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE carbon_locations
(
	entity_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	latitude VARCHAR(225) NULL,
	longitude VARCHAR(225) NULL,
	street TEXT NULL,
	city VARCHAR(40) NULL,
	state VARCHAR(10) NULL,
	elevation VARCHAR(40) NULL,
	CONSTRAINT entity_location_entity_id_uindex
		UNIQUE (entity_id),
	CONSTRAINT entity_location_entity_entity_pk_fk
		FOREIGN KEY (entity_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1
;

END;

    $db->exec($sql);
    print '<br>Table `carbon_location` Created';
}



try {
    $db->prepare('SELECT 1 FROM carbon_comments LIMIT 1;')->execute();
    print '<br>Table `carbon_comments` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE carbon_comments
(
	parent_id VARCHAR(225) NOT NULL,
	comment_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	user_id VARCHAR(225) NOT NULL,
	comment BLOB NOT NULL,
	CONSTRAINT entity_comments_entity_parent_pk_fk
		FOREIGN KEY (parent_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT entity_comments_entity_entity_pk_fk
		FOREIGN KEY (comment_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT entity_comments_entity_user_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX entity_comments_entity_parent_pk_fk
	ON carbon_comments (parent_id)
;

CREATE INDEX entity_comments_entity_user_pk_fk
	ON carbon_comments (user_id)
;
END;
    $db->exec($sql);
    print '<br>Table `carbon_comments` Created';
}


try {
    $db->prepare('SELECT 1 FROM carbon_photos LIMIT 1;')->execute();
    print '<br>Table `carbon_photos` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE carbon_photos
(
	parent_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	photo_id VARCHAR(225) NOT NULL,
	user_id VARCHAR(225) NOT NULL,
	photo_path VARCHAR(225) NOT NULL,
	photo_description TEXT NULL,
	CONSTRAINT entity_photos_photo_id_uindex
		UNIQUE (photo_id),
	CONSTRAINT photos_entity_entity_pk_fk
		FOREIGN KEY (parent_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT entity_photos_entity_entity_pk_fk
		FOREIGN KEY (photo_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT photos_entity_user_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX photos_entity_user_pk_fk
	ON carbon_photos (user_id)
;

END;

    $db->exec($sql);
    print '<br>Table `carbon_photos` Created';
}

try {
    $db->prepare('SELECT 1 FROM user_followers LIMIT 1;')->execute();
    print '<br>Table `user_followers` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE user_followers
(
	follows_user_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	user_id VARCHAR(225) NOT NULL,
	CONSTRAINT followers_entity_entity_follows_pk_fk
		FOREIGN KEY (follows_user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT followers_entity_entity_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX followers_entity_entity_pk_fk
	ON user_followers (user_id)
;



END;

    $db->exec($sql);
    print '<br>Table `user_followers` Created';

}







try {
    $db->prepare('SELECT 1 FROM user_followers LIMIT 1;')->execute();
    print '<br>Table `user_followers` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE user_followers
(
	follows_user_id VARCHAR(225) NOT NULL
		PRIMARY KEY,
	user_id VARCHAR(225) NOT NULL,
	CONSTRAINT followers_entity_entity_follows_pk_fk
		FOREIGN KEY (follows_user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT followers_entity_entity_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX followers_entity_entity_pk_fk
	ON user_followers (user_id)
;



END;

    $db->exec($sql);
    print '<br>Table `user_followers` Created';

}






try {
    $db->prepare('SELECT 1 FROM carbon_notifications LIMIT 1;')->execute();
    print '<br>Table `carbon_notifications` already exists';
} catch (PDOException $e) {
    $sql = <<<END
create table carbon_notifications
(
	notification_dismissed tinyint(1) null,
	notification_text varchar(225) null,
	notification_id varchar(225) null,
	notification_session varchar(225) null,
	constraint notifications_entity_entity_follows_pk_fk
		foreign key (notification_id) references carbon (entity_pk)
			on update cascade on delete cascade
)
;

create index notifications_entity_entity_follows_pk_fk
	on carbon_notifications (notification_id)
;


END;

    $db->exec($sql);
    print '<br>Table `carbon_notifications` Created';

}







try {
    $db->prepare('SELECT 1 FROM user_messages LIMIT 1;')->execute();
    print '<br>Table `user_messages` already exists';
} catch (PDOException $e) {
    $sql = <<<END

create table user_messages
(
    message_id varchar(225) null,
	to_user_id varchar(225) null,
	message text not null,
	message_read tinyint(1) default '0' null,
	constraint messages_entity_entity_pk_fk
		foreign key (message_id) references carbon (entity_pk)
			on update cascade on delete cascade,
	constraint messages_entity_user_from_pk_fk
		foreign key (to_user_id) references carbon (entity_pk)
			on update cascade on delete cascade
)
;

create index messages_entity_entity_pk_fk
	on user_messages (message_id)
;

create index messages_entity_user_from_pk_fk
	on user_messages (to_user_id)
;

END;

    $db->exec($sql);
    print '<br>Table `user_messages` Created';

}





try {
    $db->prepare('SELECT 1 FROM user_tasks LIMIT 1;')->execute();
    print '<br>Table `user_tasks` already exists';
} catch (PDOException $e) {
    $sql = <<<END
CREATE TABLE user_tasks
(
	task_id VARCHAR(225) NOT NULL,
	user_id VARCHAR(225) NOT NULL COMMENT 'This is the user the task is being assigned to'
		PRIMARY KEY,
	from_id VARCHAR(225) NULL COMMENT 'Keeping this colum so forgen key will remove task if user deleted',
	task_name VARCHAR(40) NOT NULL,
	task_description VARCHAR(225) NULL,
	percent_complete INT DEFAULT '0' NULL,
	start_date DATETIME NULL,
	end_date DATETIME NULL,
	CONSTRAINT tasks_entity_entity_pk_fk
		FOREIGN KEY (task_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT user_tasks_entity_user_pk_fk
		FOREIGN KEY (user_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT user_tasks_entity_entity_pk_fk
		FOREIGN KEY (from_id) REFERENCES carbon (entity_pk)
			ON UPDATE CASCADE ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARSET=latin1
;

CREATE INDEX user_tasks_entity_entity_pk_fk
	ON user_tasks (from_id)
;

CREATE INDEX user_tasks_entity_task_pk_fk
	ON user_tasks (task_id)
;

END;

    $db->exec($sql);

    print '<br>Table `user_tasks` Created';
}

print '<h4>Creating Tags</h4>';

Try {
    $sql = <<<END
REPLACE INTO carbon_tags (tag_id, tag_description, tag_name) VALUES (?,?,?);
END;

    $tag = [
        [USER,'','USER'],
        [USER_FOLLOWERS, '', 'USER_FOLLOWERS'],
        [USER_NOTIFICATIONS, '', 'USER_NOTIFICATIONS'],
        [USER_MESSAGES, '', 'USER_MESSAGES'],
        [USER_TASKS, '', 'USER_TASKS'],
        [ENTITY_COMMENTS, '', 'ENTITY_COMMENTS'],
        [ENTITY_PHOTOS, '', 'ENTITY_PHOTOS'],
        [NOTIFICATIONS, '', 'NOTIFICATIONS']
    ];

    foreach ($tag as $key => $value) {
        $db->prepare($sql)->execute($value);
    }

    print '<br>Tags inserted';

} catch (PDOException $e) {
    print '<br>' . $e->getMessage();
}

print '<br><h4>Done!</h4>';


include __DIR__ . DS . 'rootPrerogative.php';


