import {formatDistance, parseISO} from "date-fns";

const relativeDate = (date) => {
    return formatDistance(parseISO(date), new Date());
}

export {
    relativeDate,
};
