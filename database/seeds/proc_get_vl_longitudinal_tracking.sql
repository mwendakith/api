DROP PROCEDURE IF EXISTS `proc_get_vl_longitudinal_tracking`;
DELIMITER //
CREATE PROCEDURE `proc_get_vl_longitudinal_tracking`
(IN division INT(11), IN type INT(11), IN col VARCHAR(30), IN param INT(11), IN year INT(11), IN month INT(11), IN to_year INT(11), IN to_month INT(11))
BEGIN
  SET @QUERY =    "select count(gp.tests) as totals, gp.tests
                from (
                select count(*) as `tests`, facility, patient
                from viralsamples
     ";

    
    IF(division > 0) THEN 
      SET @QUERY = CONCAT(@QUERY, " join view_facilitys ON viralsamples.facility=view_facilitys.ID ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " where viralsamples.rcategory between 1 and 4 and viralsamples.flag=1 and viralsamples.repeatt=0
                 and patient != '' and patient != 'null' and patient is not null ");


    IF(type = 1) THEN 
      SET @QUERY = CONCAT(@QUERY, " and year(datetested) = ",year," ");
    END IF;

    IF(type = 3) THEN 
      SET @QUERY = CONCAT(@QUERY, " and year(datetested) = ",year," and month(datetested) = ",month," ");
    END IF;

    IF(type = 5) THEN 
      SET @QUERY = CONCAT(@QUERY, " and ((year(datetested)=",year," and month(datetested)>=",month,")
                    or (year(datetested)=",to_year," and month(datetested)<=",to_month," )
                    or (year(datetested)>",year," and year(datetested)<",to_year,"  )) ");
    END IF;

    IF(division > 0) THEN 
      SET @QUERY = CONCAT(@QUERY, " and `",col,"` = ",param," ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " group by facility, patient) gp group by gp.tests order by tests asc ");


    

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END //
DELIMITER ;
