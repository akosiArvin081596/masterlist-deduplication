export type MasterlistStatus = 'pending' | 'processing' | 'ready' | 'deduplicating' | 'completed';
export type MatchType = 'exact' | 'fuzzy' | 'typo';
export type DuplicatePairStatus = 'pending' | 'confirmed' | 'dismissed';

export type Masterlist = {
    id: number;
    user_id: number;
    incident_name: string;
    original_filename: string;
    status: MasterlistStatus;
    record_count: number | null;
    duplicate_pair_count: number | null;
    created_at: string;
    updated_at: string;
};

export type MasterlistRecord = {
    id: number;
    masterlist_id: number;
    last_name: string;
    first_name: string;
    middle_name: string | null;
    ext_name: string | null;
    birthday: string | null;
    region_name: string | null;
    province_name: string | null;
    city_name: string | null;
    barangay_name: string | null;
    purok_sitio: string | null;
    full_name: string;
    masterlist?: Masterlist;
    created_at: string;
    updated_at: string;
};

export type DuplicatePair = {
    id: number;
    masterlist_id: number;
    record_1_id: number;
    record_2_id: number;
    match_type: MatchType;
    confidence: number;
    status: DuplicatePairStatus;
    record1: MasterlistRecord;
    record2: MasterlistRecord;
    created_at: string;
    updated_at: string;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
    links: PaginationLink[];
};

export type PaginatedMasterlists = Paginated<Masterlist>;
export type PaginatedDuplicatePairs = Paginated<DuplicatePair>;
