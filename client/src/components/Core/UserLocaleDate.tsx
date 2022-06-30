const localDateOptions: Intl.DateTimeFormatOptions = {
  hour: "numeric",
  minute: "numeric",
  day: "numeric",
  month: "short",
  year: "numeric",
  hour12: false,
  timeZoneName: "short"
}

const UserLocaleDate = ({ timestamp }: { timestamp: number}) => {
  const lang = navigator.language
  const date = new Date(timestamp * 1000)
  const dateString = Intl.DateTimeFormat(lang, localDateOptions).format(date)

  return (
    <span className="timestamp" data-unix-timestamp={timestamp}>
      {dateString}
    </span>
  )
}

export default UserLocaleDate
