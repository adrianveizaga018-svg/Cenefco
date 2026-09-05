const eventDate = new Date('June 30,2026 16:37:52')
export const calculateTimeToEvent = () => {
    const currentDate = new Date()

    const timeRemaining = eventDate.getTime() - currentDate.getTime()

    const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24))
    const hours = Math.floor(
        (timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    )
    const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60))
    const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000)

    return { days, hours, minutes, seconds }
}
