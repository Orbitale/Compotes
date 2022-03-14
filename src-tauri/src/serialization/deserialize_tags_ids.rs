use core::marker::PhantomData;
use core::result::Result;
use core::result::Result::Ok;
use serde::de::Deserialize;

pub(crate) fn deserialize_tags_ids<'de, D>(deserializer: D) -> Result<Vec<u32>, D::Error>
where
    D: serde::de::Deserializer<'de>,
{
    #[derive(Debug)]
    struct StringVecVisitor(PhantomData<Vec<u32>>);

    impl<'de> serde::de::Visitor<'de> for StringVecVisitor {
        type Value = Vec<u32>;

        fn expecting(&self, formatter: &mut std::fmt::Formatter) -> std::fmt::Result {
            formatter.write_str("a string containing a list of integer IDs separated by commas.")
        }

        fn visit_str<E>(self, v: &str) -> Result<Self::Value, E>
            where E: serde::de::Error,
        {
            let mut ids = Vec::new();
            for id in v.split(',') {
                let id = id.parse::<u32>().unwrap_or(0);
                if id != 0 {
                    ids.push(id);
                }
            }
            Ok(ids)
        }

        fn visit_none<E>(self) -> Result<Self::Value, E>
            where E: serde::de::Error
        {
            Ok(Vec::new())
        }

        fn visit_seq<S>(self, visitor: S) -> Result<Self::Value, S::Error>
            where S: serde::de::SeqAccess<'de>
        {
            Deserialize::deserialize(serde::de::value::SeqAccessDeserializer::new(visitor))
        }
    }

    deserializer.deserialize_any(StringVecVisitor(PhantomData))
}
