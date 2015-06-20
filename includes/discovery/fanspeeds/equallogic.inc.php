<?php

if ($device['os'] == "equallogic") {
    $oids = snmp_walk($device, "eqlMemberHealthDetailsFanName", "-OQn", "EQLMEMBER-MIB", $config['install_dir']."/mibs/equallogic");

    /**
.1.3.6.1.4.1.12740.2.1.7.1.2.1.329840783.1 = Power Cooling Module 0 Fan 0
.1.3.6.1.4.1.12740.2.1.7.1.2.1.329840783.2 = Power Cooling Module 0 Fan 1
.1.3.6.1.4.1.12740.2.1.7.1.2.1.329840783.3 = Power Cooling Module 1 Fan 0
.1.3.6.1.4.1.12740.2.1.7.1.2.1.329840783.4 = Power Cooling Module 1 Fan 1
    **/

    d_echo($oids."\n");
    if (!empty($oids)) {
        echo("EQLCONTROLLER-MIB ");
        foreach (explode("\n",$oids) as $data) {
            $data = trim($data);
            if (!empty($data)) {
                list($oid,$descr) = explode(" = ", $data,2);
                $split_oid = explode('.', $oid);
                $num_index = $split_oid[count($split_oid)-1];
                $index = $num_index;
                $part_oid = $split_oid[count($split_oid)-2];
                $num_index = $part_oid.'.'.$num_index;
                $base_oid = '.1.3.6.1.4.1.12740.2.1.7.1.3.1.';
                $oid = $base_oid .$num_index;
                $extra = snmp_get_multi($device, "eqlMemberHealthDetailsFanValue.3.329840783.$index eqlMemberHealthDetailsFanCurrentState.3.329840783.$index eqlMemberHealthDetailsFanHighCriticalThreshold.3.329840783.$index eqlMemberHealthDetailsFanHighWarningThreshold.3.329840783.$index eqlMemberHealthDetailsFanLowCriticalThreshold.3.329840783.$index eqlMemberHealthDetailsFanLowWarningThreshold.3.329840783.$index", "-OQUs", "EQLMEMBER-MIB", $config['install_dir']."/mibs/equallogic");
                $keys = array_keys($extra);
                $temperature = $extra[$keys[0]]['eqlMemberHealthDetailsFanValue'];
                $low_limit = $extra[$keys[0]]['eqlMemberHealthDetailsFanLowCriticalThreshold'];
                $low_warn = $extra[$keys[0]]['eqlMemberHealthDetailsFanLowWarningThreshold'];
                $high_limit = $extra[$keys[0]]['eqlMemberHealthDetailsFanHighCriticalThreshold'];
                $high_warn = $extra[$keys[0]]['eqlMemberHealthDetailsFanHighWarningThreshold'];
                $index = 100+$index;

                if ($extra[$keys[0]]['eqlMemberHealthDetailsFanCurrentState'] != 'unknown') {
                    discover_sensor($valid['sensor'], 'fanspeed', $device, $oid, $index, 'snmp', $descr, 1, 1, $low_limit, $low_warn, $high_limit, $high_warn, $temperature);
                }
            }
        }
    }
}