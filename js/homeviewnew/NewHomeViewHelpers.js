// Additional definitions to help with simplicity.
studyTypes[0]                            = "Unset";
deviceTypes[0]                           = "Unset";
officeTypes[0]                           = "Unset";


function getStudyTypes() {
    return studyTypes.slice(1, studyTypes.length);
}

function getDeviceTypes() {
    return deviceTypes.slice(1, deviceTypes.length);
}

function getOfficeTypes() {
    return officeTypes.slice(1, officeTypes.length);
}

function convertIndexToStudyType(index) {
    return Number(index) + 1;
}

function convertIndexToDeviceType(index) {
    return Number(index) + 1;
}

function convertIndexToOfficeType(index) {
    return Number(index) + 1;
}

function getAllowedDevicesForStudyType(studyType) {
    studyType = Number(studyType);
    switch (studyType)
    {
        case STUDY_TYPE_TMC:
            return ["KapturrKam", "Miovision Scout"];
        case STUDY_TYPE_ROADWAY:
            return ["Metrocount"];
        case STUDY_TYPE_ORIGINDESTINATION:
            return ["KapturrKam", "Miovision Scout"];
        case STUDY_TYPE_ADT:
            return ["KapturrKam", "Miovision Scout"];
        default:
            return [];
    }
}

function isInt(value) {
  var x = parseFloat(value);
  return !isNaN(value) && (x | 0) === x;
}