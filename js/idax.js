// Method call parameters
const METHODCALL_HEADER_PARAM_AUTHTOKEN          = "_mhdr_token";
const METHODCALL_HEADER_PARAM_DEVICEID           = "_mhdr_deviceid";
const METHODCALL_HEADER_PARAM_COMPRESSRESPONSE   = "_mhdr_compressresponse";
const METHODCALL_HEADER_PARAM_UTF8ENCODERESPONSE = "_mhdr_utf8encode";
const METHODCALL_HEADER_PARAM_APP_BUILD          = "_mhdr_build";
const METHODCALL_HEADER_PARAM_APP_VERSION        = "_mhdr_version";

// Info levels
const INFO_LEVEL_BASIC             = 1;
const INFO_LEVEL_SUMMARY           = 2;
const INFO_LEVEL_FULL              = 3;

// Device types
const DEVICE_TYPE_METROCOUNT       = 1;
const DEVICE_TYPE_KAPTURRKAM       = 2;
const DEVICE_TYPE_MIOVISIONSCOUNT  = 3; 

// Office types.
const WA_OFFICE_TYPE = 1;
const CA_OFFICE_TYPE = 2;
const CO_OFFICE_TYPE = 3;
const ME_OFFICE_TYPE = 4;
const OT_OFFICE_TYPE = 5;

const deviceTypes = [];
deviceTypes[DEVICE_TYPE_METROCOUNT]      = "Metrocount";
deviceTypes[DEVICE_TYPE_KAPTURRKAM]      = "KapturrKam";
deviceTypes[DEVICE_TYPE_MIOVISIONSCOUNT] = "Miovision Scout";

// Study types
const STUDY_TYPE_TMC               = 1;
const STUDY_TYPE_ROADWAY           = 2;
const STUDY_TYPE_ORIGINDESTINATION = 3;
const STUDY_TYPE_ADT               = 4;

const studyTypes = [];
studyTypes[STUDY_TYPE_TMC]               = "TMC";
studyTypes[STUDY_TYPE_ROADWAY]           = "Roadway";
studyTypes[STUDY_TYPE_ORIGINDESTINATION] = "Origin Destination";
studyTypes[STUDY_TYPE_ADT]               = "ADT";

const officeTypes = [];
officeTypes[WA_OFFICE_TYPE] = "WA";
officeTypes[CA_OFFICE_TYPE] = "CA";
officeTypes[CO_OFFICE_TYPE] = "CO";
officeTypes[ME_OFFICE_TYPE] = "ME";
officeTypes[OT_OFFICE_TYPE] = "OT";

