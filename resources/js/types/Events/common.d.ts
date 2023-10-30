import {Category} from "@/types/Categories/common";

export type Event = {
    id: number
    name: string
    start_date: string
    end_date: string
    description: string
    category_id: number
    category: Category
    image: Image
}

export type Image = {
    id: number
    filename: string
}
